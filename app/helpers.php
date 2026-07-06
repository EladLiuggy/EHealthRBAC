<?php
declare(strict_types=1);

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function siteUrl(string $path = ''): string {
    $base = rtrim(BASE_URL, '/');

    if ($path === '') {
        return $base;
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return $base . '/' . ltrim($path, '/');
}

function assetUrl(string $path): string {
    return siteUrl('assets/' . ltrim($path, '/'));
}

function uploadUrl(string $path = ''): string {
    return siteUrl('uploads/licenses/' . ltrim($path, '/'));
}

function redirect(string $path): never {
    header('Location: ' . siteUrl($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    return null;
}

function generateSystemId(string $role, PDO $pdo): string {
    $prefixes = [
        'patient' => 'PAT',
        'doctor' => 'DOC',
        'nurse' => 'NUR',
        'admin' => 'ADM'
    ];

    $prefix = $prefixes[$role] ?? 'USR';
    $year = date('Y');

    $stmt = $pdo->prepare("SELECT COUNT(*) + 1 AS next_id FROM users WHERE role = :role");
    $stmt->execute(['role' => $role]);
    $next = (int)$stmt->fetchColumn();

    return $prefix . '-' . $year . '-' . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
}

function logAction(?int $userId, string $action): void {
    $pdo = db();
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, ip_address, user_agent)
        VALUES (:user_id, :action, :ip, :agent)
    ");
    $stmt->execute([
        'user_id' => $userId,
        'action' => $action,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
}

function strongPassword(string $password): bool {
    return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}
