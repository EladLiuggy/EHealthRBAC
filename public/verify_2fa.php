<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (!isTwoFactorEnabled()) {
    flash('error', 'Two-factor verification is currently disabled.');
    redirect('/login.php');
}

if ($loggedInUser = currentUser()) {
    redirect(dashboardPath($loggedInUser['role']));
}

$pending = requirePendingTwoFactor();
verifyCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'verify';

    if ($action === 'resend') {
        try {
            // Resending keeps the current pending login, but rotates the usable OTP.
            resendTwoFactorOtp();
            flash('success', 'A new verification code has been sent to your email.');
        } catch (Throwable $e) {
            flash('error', 'A new verification code could not be sent right now.');
        }

        redirect('/verify_2fa.php');
    }

    $otpCode = trim($_POST['otp_code'] ?? '');
    if (!preg_match('/^\d{6}$/', $otpCode)) {
        recordTwoFactorAttempt();

        if (remainingTwoFactorAttempts() <= 0) {
            $user = getTwoFactorUser((int)$pending['user_id']);
            if ($user) {
                logAction((int)$user['id'], 'Too many invalid 2FA attempts');
            }

            clearPendingTwoFactor();
            flash('error', 'Too many invalid verification attempts. Please log in again.');
            redirect('/login.php');
        }

        flash('error', 'Enter the 6-digit verification code from your email.');
        redirect('/verify_2fa.php');
    }

    if (verifyTwoFactorOtp($otpCode)) {
        completeTwoFactorLogin();
    }

    if (remainingTwoFactorAttempts() <= 0) {
        $user = getTwoFactorUser((int)$pending['user_id']);
        if ($user) {
            logAction((int)$user['id'], 'Too many invalid 2FA attempts');
        }

        clearPendingTwoFactor();
        flash('error', 'Too many invalid verification attempts. Please log in again.');
        redirect('/login.php');
    }

    flash('error', 'Invalid or expired verification code.');
    redirect('/verify_2fa.php');
}

$pending = requirePendingTwoFactor();
$remainingAttempts = remainingTwoFactorAttempts();
$resendAvailableAt = (int)($pending['resend_available_at'] ?? 0);
$cooldownSeconds = max(0, $resendAvailableAt - time());

renderHeader('Verify Login');
?>
<div class="form-card">
    <h1>Two-Factor Verification</h1>
    <p>Enter the 6-digit code sent to <strong><?= e((string)$pending['email']) ?></strong>.</p>
    <p>Your code expires in <?= TWO_FACTOR_OTP_TTL_MINUTES ?> minutes. Attempts remaining: <?= e((string)$remainingAttempts) ?></p>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="verify">

        <label>Verification Code</label>
        <input
            type="text"
            name="otp_code"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            pattern="\d{6}"
            required
        >

        <button type="submit" style="margin-top:1.2rem">Verify Code</button>
    </form>

    <form method="post" style="margin-top:1rem">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="resend">
        <button type="submit" class="btn outline" <?= $cooldownSeconds > 0 ? 'disabled' : '' ?>>Resend Code</button>
    </form>

    <?php if ($cooldownSeconds > 0): ?>
        <p style="margin-top:0.9rem">You can request another code in <?= e((string)$cooldownSeconds) ?> seconds.</p>
    <?php endif; ?>
</div>
<?php renderFooter(); ?>
