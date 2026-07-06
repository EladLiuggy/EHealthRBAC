<?php
require_once __DIR__ . '/../app/bootstrap.php';
requireRole('admin');
$logs = db()->query("
    SELECT al.*, u.full_name, u.system_id
    FROM audit_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 100
")->fetchAll();

renderHeader('Audit Logs');
?>
<h1>Audit Logs</h1>
<div class="table-wrap">
<table>
<tr><th>Date</th><th>User</th><th>Action</th><th>IP Address</th></tr>
<?php foreach ($logs as $log): ?>
<tr>
    <td><?= e($log['created_at']) ?></td>
    <td><?= e(($log['system_id'] ?? 'Guest') . ' ' . ($log['full_name'] ?? '')) ?></td>
    <td><?= e($log['action']) ?></td>
    <td><?= e($log['ip_address'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php renderFooter(); ?>
