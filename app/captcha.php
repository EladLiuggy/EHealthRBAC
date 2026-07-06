<?php
declare(strict_types=1);

function createCaptcha(): string {
    $a = random_int(2, 9);
    $b = random_int(2, 9);
    $_SESSION['captcha_answer'] = (string)($a + $b);
    return "What is {$a} + {$b}?";
}

function captchaRequired(string $email): bool {
    $pdo = db();
    $stmt = $pdo->prepare("
        SELECT attempts FROM login_attempts
        WHERE email = :email AND ip_address = :ip
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([
        'email' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    $attempts = (int)($stmt->fetchColumn() ?: 0);

    return $attempts >= 3;
}

function verifyCaptchaAnswer(?string $answer): bool {
    return isset($_SESSION['captcha_answer']) && trim((string)$answer) === $_SESSION['captcha_answer'];
}

function recordFailedLogin(string $email): void {
    $pdo = db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $stmt = $pdo->prepare("
        SELECT id, attempts FROM login_attempts
        WHERE email = :email AND ip_address = :ip
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute(['email' => $email, 'ip' => $ip]);
    $row = $stmt->fetch();

    if ($row) {
        $stmt = $pdo->prepare("
            UPDATE login_attempts
            SET attempts = attempts + 1, last_attempt = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute(['id' => $row['id']]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO login_attempts (email, ip_address, attempts)
            VALUES (:email, :ip, 1)
        ");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
    }
}

function clearFailedLogin(string $email): void {
    $pdo = db();
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE email = :email AND ip_address = :ip");
    $stmt->execute([
        'email' => $email,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
}
