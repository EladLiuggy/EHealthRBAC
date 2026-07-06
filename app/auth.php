<?php
declare(strict_types=1);

function currentUser(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
}

function logoutUser(): void {
    clearPendingTwoFactor();
    $_SESSION = [];
    session_destroy();
}

function requireLogin(): array {
    $user = currentUser();
    if (!$user) {
        redirect('/login.php');
    }
    return $user;
}

function requireRole(string|array $roles): array {
    $user = requireLogin();
    $roles = (array)$roles;

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        require __DIR__ . '/../public/403.php';
        exit;
    }

    if (in_array($user['role'], ['doctor', 'nurse'], true) && $user['status'] !== 'approved') {
        flash('error', 'Your account is still pending admin verification.');
        redirect('/pending.php');
    }

    return $user;
}

function dashboardPath(string $role): string {
    return match ($role) {
        'admin' => siteUrl('dashboards/admin.php'),
        'doctor' => siteUrl('dashboards/doctor.php'),
        'nurse' => siteUrl('dashboards/nurse.php'),
        'patient' => siteUrl('dashboards/patient.php'),
        default => siteUrl('login.php')
    };
}
