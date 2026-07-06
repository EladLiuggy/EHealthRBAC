<?php require_once __DIR__ . '/../app/bootstrap.php'; renderHeader('Pending Verification'); ?>
<div class="form-card">
    <h1>Account Pending</h1>
    <p>Your doctor/nurse account has been created but must be approved before you can access your dashboard.</p>
    <a class="btn" href="/login.php">Back to Login</a>
</div>
<?php renderFooter(); ?>
