<?php
declare(strict_types=1);

function pendingTwoFactor(): ?array {
    if (empty($_SESSION['pending_2fa']) || !is_array($_SESSION['pending_2fa'])) {
        return null;
    }

    return $_SESSION['pending_2fa'];
}

function hasPendingTwoFactor(): bool {
    return pendingTwoFactor() !== null;
}

function clearPendingTwoFactor(): void {
    unset($_SESSION['pending_2fa']);
}

function beginTwoFactorChallenge(array $user): void {
    $_SESSION['pending_2fa'] = [
        'user_id' => (int)$user['id'],
        'role' => $user['role'],
        'email' => $user['email'],
        'attempts' => 0,
        'resend_available_at' => 0,
    ];

    issueTwoFactorOtp($user, false);
}

function requirePendingTwoFactor(): array {
    $pending = pendingTwoFactor();

    if (!$pending) {
        flash('error', 'Your verification session has expired. Please log in again.');
        redirect('/login.php');
    }

    return $pending;
}

function getTwoFactorUser(int $userId): ?array {
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch() ?: null;
}

function issueTwoFactorOtp(array $user, bool $isResend): void {
    $pdo = db();
    $otpCode = (string) random_int(100000, 999999);

    $pdo->beginTransaction();

    try {
        // Invalidate any older pending codes so only the latest OTP can be used.
        $invalidateStmt = $pdo->prepare('
            UPDATE two_factor_codes
            SET is_used = TRUE
            WHERE user_id = :user_id AND is_used = FALSE
        ');
        $invalidateStmt->execute(['user_id' => $user['id']]);

        $insertStmt = $pdo->prepare('
            INSERT INTO two_factor_codes (user_id, otp_code, expires_at, is_used, created_at)
            VALUES (:user_id, :otp_code, CURRENT_TIMESTAMP + (:ttl || \' minutes\')::interval, FALSE, CURRENT_TIMESTAMP)
        ');
        $insertStmt->execute([
            'user_id' => $user['id'],
            'otp_code' => $otpCode,
            'ttl' => TWO_FACTOR_OTP_TTL_MINUTES,
        ]);

        sendTwoFactorOtpEmail($user['email'], $user['full_name'], $otpCode);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }

    $_SESSION['pending_2fa']['resend_available_at'] = time() + TWO_FACTOR_RESEND_COOLDOWN_SECONDS;
    logAction((int)$user['id'], $isResend ? 'Resent login OTP' : 'Sent login OTP');
}

function remainingTwoFactorAttempts(): int {
    $pending = pendingTwoFactor();
    $used = (int)($pending['attempts'] ?? 0);
    return max(0, TWO_FACTOR_MAX_ATTEMPTS - $used);
}

function canResendTwoFactor(): bool {
    $pending = pendingTwoFactor();
    if (!$pending) {
        return false;
    }

    return time() >= (int)($pending['resend_available_at'] ?? 0);
}

function resendTwoFactorOtp(): void {
    $pending = requirePendingTwoFactor();

    if (!canResendTwoFactor()) {
        flash('error', 'Please wait before requesting another verification code.');
        redirect('/verify_2fa.php');
    }

    $user = getTwoFactorUser((int)$pending['user_id']);
    if (!$user) {
        clearPendingTwoFactor();
        flash('error', 'Your account could not be loaded. Please log in again.');
        redirect('/login.php');
    }

    issueTwoFactorOtp($user, true);
}

function recordTwoFactorAttempt(): void {
    if (!hasPendingTwoFactor()) {
        return;
    }

    $_SESSION['pending_2fa']['attempts'] = (int)($_SESSION['pending_2fa']['attempts'] ?? 0) + 1;
}

function verifyTwoFactorOtp(string $otpCode): bool {
    $pending = requirePendingTwoFactor();
    recordTwoFactorAttempt();

    $stmt = db()->prepare('
        SELECT id
        FROM two_factor_codes
        WHERE user_id = :user_id
          AND otp_code = :otp_code
          AND is_used = FALSE
          AND expires_at >= CURRENT_TIMESTAMP
        ORDER BY id DESC
        LIMIT 1
    ');
    $stmt->execute([
        'user_id' => $pending['user_id'],
        'otp_code' => $otpCode,
    ]);

    $codeId = $stmt->fetchColumn();
    if (!$codeId) {
        return false;
    }

    $updateStmt = db()->prepare('
        UPDATE two_factor_codes
        SET is_used = TRUE
        WHERE id = :id
    ');
    $updateStmt->execute(['id' => $codeId]);

    return true;
}

function completeTwoFactorLogin(): never {
    $pending = requirePendingTwoFactor();
    $user = getTwoFactorUser((int)$pending['user_id']);

    if (!$user) {
        clearPendingTwoFactor();
        flash('error', 'Your account could not be loaded. Please log in again.');
        redirect('/login.php');
    }

    clearPendingTwoFactor();
    loginUser($user);
    logAction((int)$user['id'], 'Logged in');
    redirect(dashboardPath($user['role']));
}
