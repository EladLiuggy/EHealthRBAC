<?php
require_once __DIR__ . '/../app/bootstrap.php';

$pdo = db();

$email = 'admin@ehealth.local';
$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    UPDATE users
    SET password_hash = :hash,
        status = 'approved'
    WHERE email = :email
");

$stmt->execute([
    'hash' => $hash,
    'email' => $email
]);

$pdo->prepare("DELETE FROM login_attempts WHERE email = :email")
    ->execute(['email' => $email]);

echo "<h2>Admin password reset successfully.</h2>";
echo "<p>Email: admin@ehealth.local</p>";
echo "<p>Password: Admin@123</p>";
echo "<p><a href='/login.php'>Go to Login</a></p>";