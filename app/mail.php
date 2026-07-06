<?php
declare(strict_types=1);

function sendTwoFactorOtpEmail(string $recipientEmail, string $recipientName, string $otpCode): void {
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';

    if (!is_file($autoloadPath)) {
        throw new RuntimeException('PHPMailer is not installed. Run composer install before using email 2FA.');
    }

    require_once $autoloadPath;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->Port = MAIL_PORT;
        $mail->SMTPAuth = MAIL_USERNAME !== '' || MAIL_PASSWORD !== '';

        if ($mail->SMTPAuth) {
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
        }

        if (MAIL_ENCRYPTION === 'tls') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } elseif (MAIL_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        }

        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);
        $mail->isHTML(true);
        $mail->Subject = APP_NAME . ' login verification code';

        // Keep the message simple so it renders well across mail clients.
        $mail->Body = '<p>Hello ' . e($recipientName) . ',</p>'
            . '<p>Your one-time verification code is <strong>' . e($otpCode) . '</strong>.</p>'
            . '<p>This code expires in ' . TWO_FACTOR_OTP_TTL_MINUTES . ' minutes.</p>'
            . '<p>If you did not try to sign in, please ignore this email.</p>';
        $mail->AltBody = "Hello {$recipientName},\n\n"
            . "Your one-time verification code is {$otpCode}.\n"
            . 'This code expires in ' . TWO_FACTOR_OTP_TTL_MINUTES . " minutes.\n\n"
            . "If you did not try to sign in, please ignore this email.";

        $mail->send();
    } catch (Throwable $e) {
        throw new RuntimeException('Unable to send verification email: ' . $e->getMessage(), 0, $e);
    }
}
