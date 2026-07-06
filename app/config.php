<?php
declare(strict_types=1);

function envConfig(string $key, string $default = ''): string {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return (string)$value;
}

define('APP_NAME', 'E-Health RBAC');
// Include the full app path here if you are serving from a subfolder, for example:
// http://localhost/EHealthRBAC/public
define('BASE_URL', envConfig('BASE_URL', 'http://localhost:8000'));

define('DB_HOST', envConfig('DB_HOST', 'localhost'));
define('DB_PORT', envConfig('DB_PORT', '5432'));
define('DB_NAME', envConfig('DB_NAME', 'ehealth_rbac'));
define('DB_USER', envConfig('DB_USER', 'postgres'));
define('DB_PASS', envConfig('DB_PASS', '')); // Set with an environment variable in production.

define('UPLOAD_DIR', envConfig('UPLOAD_DIR', __DIR__ . '/../public/uploads/licenses/'));
define('UPLOAD_URL', envConfig('UPLOAD_URL', '/uploads/licenses/'));

// Local development defaults use a mail catcher such as Mailpit.
// Switch these to your real SMTP provider only when you want external delivery.
define('MAIL_FROM_ADDRESS', envConfig('MAIL_FROM_ADDRESS', 'no-reply@ehealth.local'));
define('MAIL_FROM_NAME', envConfig('MAIL_FROM_NAME', APP_NAME));
define('MAIL_HOST', envConfig('MAIL_HOST', '127.0.0.1'));
define('MAIL_PORT', (int) envConfig('MAIL_PORT', '1025'));
define('MAIL_USERNAME', envConfig('MAIL_USERNAME', ''));
define('MAIL_PASSWORD', envConfig('MAIL_PASSWORD', ''));
define('MAIL_ENCRYPTION', envConfig('MAIL_ENCRYPTION', ''));

define('TWO_FACTOR_OTP_TTL_MINUTES', 10);
define('TWO_FACTOR_MAX_ATTEMPTS', 3);
define('TWO_FACTOR_RESEND_COOLDOWN_SECONDS', 60);
