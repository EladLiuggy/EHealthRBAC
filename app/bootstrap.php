<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/captcha.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/two_factor.php';

function renderHeader(string $title): void {
    $user = currentUser();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> | <?= APP_NAME ?></title>
        <link rel="stylesheet" href="<?= e(assetUrl('css/style.css')) ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
    <body>
    <nav class="navbar">
        <a href="<?= e(siteUrl()) ?>" class="brand"><i class="bi bi-heart-pulse-fill"></i> <?= APP_NAME ?></a>
        <div class="nav-links">
            <?php if ($user): ?>
                <a href="<?= e(siteUrl()) ?>">Home</a>
                <a href="<?= e(dashboardPath($user['role'])) ?>">Dashboard</a>
                <a href="<?= e(siteUrl('profile.php')) ?>">Profile</a>
                <a class="btn small" href="<?= e(siteUrl('logout.php')) ?>">Logout</a>
            <?php else: ?>
                <a href="<?= e(siteUrl()) ?>">Home</a>
                <a href="<?= e(siteUrl('login.php')) ?>">Login</a>
                <a class="btn small" href="<?= e(siteUrl('register.php')) ?>">Create Account</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container page-animate">
    <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
    <?php
}

function renderFooter(): void {
    ?>
    </main>
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3><?= APP_NAME ?></h3>
                <p>Secure electronic health records with role-based access control.</p>
            </div>
            <div>
                <h4>System Roles</h4>
                <p>Admin, Doctor, Nurse, Patient</p>
            </div>
            <div>
                <h4>Security</h4>
                <p>Password hashing, staff verification, audit logs, and access control.</p>
            </div>
        </div>
        <p class="footer-bottom"><?= APP_NAME ?> &copy; <?= date('Y') ?> | B.Tech Software Engineering Project</p>
    </footer>
    </body>
    </html>
    <?php
}
