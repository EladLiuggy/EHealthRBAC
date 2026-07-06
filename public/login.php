<?php
require_once __DIR__ . '/../app/bootstrap.php';

if ($loggedInUser = currentUser()) {
    redirect(dashboardPath($loggedInUser['role']));
}

if (hasPendingTwoFactor()) {
    redirect('/verify_2fa.php');
}

verifyCsrf();

$emailForCaptcha = strtolower(trim($_POST['email'] ?? $_GET['email'] ?? ''));
$showCaptcha = $emailForCaptcha ? captchaRequired($emailForCaptcha) : false;
$captchaQuestion = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (captchaRequired($email) && !verifyCaptchaAnswer($_POST['captcha'] ?? null)) {
        flash('error', 'Robot check failed.');
        redirect('/login.php?email=' . urlencode($email));
    }

    $stmt = db()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        recordFailedLogin($email);
        flash('error', 'Invalid email or password.');
        redirect('/login.php?email=' . urlencode($email));
    }

    if ($user['status'] === 'rejected') {
        flash('error', 'Your account was rejected. Contact administrator.');
        redirect('/login.php');
    }

    if (in_array($user['role'], ['doctor', 'nurse'], true) && $user['status'] === 'pending') {
        flash('error', 'Your account is pending admin verification.');
        redirect('/pending.php');
    }

    clearFailedLogin($email);

    try {
        beginTwoFactorChallenge($user);
        flash('success', 'A verification code has been sent to your email address.');
        redirect('/verify_2fa.php');
    } catch (Throwable $e) {
        clearPendingTwoFactor();
        logAction((int)$user['id'], 'Failed to send login OTP');
        flash('error', 'Login could not be completed because the verification email was not sent.');
        redirect('/login.php?email=' . urlencode($email));
    }
}

if ($showCaptcha) {
    $captchaQuestion = createCaptcha();
}
renderHeader('Login');
?>
<div class="form-card">
    <h1>Login</h1>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Email</label>
        <input type="email" name="email" value="<?= e($emailForCaptcha) ?>" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <?php if ($showCaptcha): ?>
            <label><?= e($captchaQuestion) ?></label>
            <input name="captcha" required>
        <?php endif; ?>

        <button type="submit" style="margin-top:1.2rem">Login</button>
    </form>
</div>
<?php renderFooter(); ?>
