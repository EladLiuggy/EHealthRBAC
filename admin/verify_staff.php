<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('admin');
$pdo = db();
verifyCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetId = (int)($_POST['user_id'] ?? 0);
    $decision = $_POST['decision'] ?? '';
    $comment = trim($_POST['comment'] ?? '');

    if (!in_array($decision, ['approved', 'rejected'], true)) {
        flash('error', 'Invalid decision.');
        redirect('/admin/verify_staff.php');
    }

    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id AND role IN ('doctor','nurse')");
    $stmt->execute(['status' => $decision, 'id' => $targetId]);

    $stmt = $pdo->prepare("
        UPDATE staff_verifications
        SET verification_status = :status, admin_comment = :comment, verification_method = 'manual'
        WHERE user_id = :id
    ");
    $stmt->execute(['status' => $decision, 'comment' => $comment, 'id' => $targetId]);

    logAction((int)$user['id'], "Admin {$decision} staff account ID {$targetId}");
    flash('success', "Staff account {$decision}.");
    redirect('/admin/verify_staff.php');
}

$stmt = $pdo->query("
    SELECT u.id, u.system_id, u.full_name, u.email, u.role, u.status,
           sv.license_number, sv.license_file, sv.verification_method, sv.verification_status
    FROM users u
    JOIN staff_verifications sv ON sv.user_id = u.id
    WHERE u.role IN ('doctor','nurse')
    ORDER BY u.created_at DESC
");
$staff = $stmt->fetchAll();

renderHeader('Verify Staff');
?>
<h1>Pending Staff Verification</h1>
<div class="table-wrap">
<table>
<tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th><th>License</th><th>File</th><th>Status</th><th>Action</th></tr>
<?php foreach ($staff as $s): ?>
<tr>
    <td><?= e($s['system_id']) ?></td>
    <td><?= e($s['full_name']) ?></td>
    <td><?= e($s['role']) ?></td>
    <td><?= e($s['email']) ?></td>
    <td><?= e($s['license_number']) ?><br><small><?= e($s['verification_method']) ?></small></td>
    <td>
        <?php if ($s['license_file']): ?>
            <a class="btn small outline" target="_blank" href="<?= e(uploadUrl($s['license_file'])) ?>">View</a>
        <?php endif; ?>
    </td>
    <td><span class="badge <?= e($s['status']) ?>"><?= e($s['status']) ?></span></td>
    <td>
        <form method="post" style="display:grid;gap:.4rem">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="user_id" value="<?= (int)$s['id'] ?>">
            <input name="comment" placeholder="Admin comment">
            <button name="decision" value="approved" class="btn small">Approve</button>
            <button name="decision" value="rejected" class="btn small danger">Reject</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php renderFooter(); ?>
