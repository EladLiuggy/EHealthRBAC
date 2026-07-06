<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('admin');
$pdo = db();

$stats = [];
foreach (['patient', 'doctor', 'nurse'] as $role) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = :role");
    $stmt->execute(['role' => $role]);
    $stats[$role] = (int)$stmt->fetchColumn();
}

renderHeader('Admin Dashboard');
?>
<div class="dashboard-title">
    <h1>Admin Dashboard</h1>
    <span class="badge approved"><?= e($user['system_id']) ?></span>
</div>

<section class="grid cards">
    <div class="card"><i class="bi bi-people"></i><h3>Patients</h3><p><?= $stats['patient'] ?></p></div>
    <div class="card"><i class="bi bi-person-badge"></i><h3>Doctors</h3><p><?= $stats['doctor'] ?></p></div>
    <div class="card"><i class="bi bi-heart-pulse"></i><h3>Nurses</h3><p><?= $stats['nurse'] ?></p></div>
    <div class="card"><i class="bi bi-shield-check"></i><h3>Security</h3><p>RBAC Active</p></div>
</section>

<section class="grid cards" style="margin-top:1rem">
    <a class="card" href="/admin/manage_users.php"><i class="bi bi-person-gear"></i><h3>Manage Users</h3><p>Create control through role/status updates, edit users, and delete accounts.</p></a>
    <a class="card" href="/admin/verify_staff.php"><i class="bi bi-patch-check"></i><h3>Verify Staff</h3><p>Approve/reject doctors and nurses.</p></a>
    <a class="card" href="/admin/assign_patients.php"><i class="bi bi-diagram-3"></i><h3>Assign Patients</h3><p>Assign patients to doctors and nurses.</p></a>
    <a class="card" href="/admin/audit_logs.php"><i class="bi bi-activity"></i><h3>Audit Logs</h3><p>Track important activities.</p></a>
</section>
<?php renderFooter(); ?>
