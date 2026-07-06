<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = currentUser();
if ($user) {
    logAction((int)$user['id'], 'Logged out');
} elseif (hasPendingTwoFactor()) {
    $pending = pendingTwoFactor();
    if (!empty($pending['user_id'])) {
        logAction((int)$pending['user_id'], 'Logged out during 2FA verification');
    }
}
logoutUser();
redirect('/login.php');
