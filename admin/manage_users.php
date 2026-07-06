<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('admin');
$pdo = db();
verifyCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetId = (int)($_POST['user_id'] ?? 0);

    if (!$targetId) {
        flash('error', 'Invalid user selected.');
        redirect('/admin/manage_users.php');
    }

    if ($targetId === (int)$user['id'] && in_array($action, ['delete', 'change_role'], true)) {
        flash('error', 'You cannot delete your own account or change your own role.');
        redirect('/admin/manage_users.php');
    }

    if ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $targetId]);
        logAction((int)$user['id'], "Deleted user account ID {$targetId}");
        flash('success', 'User deleted successfully.');
        redirect('/admin/manage_users.php');
    }

    if ($action === 'update') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $dateOfBirth = $_POST['date_of_birth'] ?: null;
        $gender = $_POST['gender'] ?: null;
        $status = $_POST['status'] ?? 'approved';
        $role = $_POST['role'] ?? '';

        if (!$fullName || !$email || !in_array($status, ['pending', 'approved', 'rejected'], true) || !in_array($role, ['admin', 'doctor', 'nurse', 'patient'], true)) {
            flash('error', 'Invalid user details.');
            redirect('/admin/manage_users.php');
        }

        $stmt = $pdo->prepare("
            UPDATE users
            SET full_name = :full_name,
                email = :email,
                role = :role,
                status = :status,
                phone = :phone,
                address = :address,
                date_of_birth = :date_of_birth,
                gender = :gender
            WHERE id = :id
        ");
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role,
            'status' => $status,
            'phone' => $phone,
            'address' => $address,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'id' => $targetId
        ]);

        if ($role === 'patient') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $targetId]);

            if ((int)$stmt->fetchColumn() === 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO patients (user_id, date_of_birth, gender, phone, address)
                    VALUES (:user_id, :dob, :gender, :phone, :address)
                ");
            } else {
                $stmt = $pdo->prepare("
                    UPDATE patients
                    SET date_of_birth = :dob, gender = :gender, phone = :phone, address = :address
                    WHERE user_id = :user_id
                ");
            }

            $stmt->execute([
                'user_id' => $targetId,
                'dob' => $dateOfBirth,
                'gender' => $gender,
                'phone' => $phone,
                'address' => $address
            ]);
        }

        logAction((int)$user['id'], "Updated user account ID {$targetId}");
        flash('success', 'User updated successfully.');
        redirect('/admin/manage_users.php');
    }
}

$users = $pdo->query("
    SELECT id, system_id, full_name, email, role, status, phone, address, date_of_birth, gender, created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll();

renderHeader('Manage Users');
?>
<div class="dashboard-title">
    <h1>Manage Users</h1>
    <a class="btn small outline" href="/dashboards/admin.php"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<p>This page allows the administrator to manage user accounts, assign roles, update information, approve/reject accounts, and delete users.</p>

<div class="table-wrap">
<table>
    <tr>
        <th>ID</th>
        <th>User Details</th>
        <th>Role / Status</th>
        <th>Contact / Bio</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <td>
                    <strong><?= e($u['system_id']) ?></strong><br>
                    <small><?= e($u['created_at']) ?></small>
                </td>
                <td>
                    <label>Full Name</label>
                    <input name="full_name" value="<?= e($u['full_name']) ?>" required>

                    <label>Email</label>
                    <input type="email" name="email" value="<?= e($u['email']) ?>" required>
                </td>
                <td>
                    <label>Role</label>
                    <select name="role">
                        <?php foreach (['admin', 'doctor', 'nurse', 'patient'] as $role): ?>
                            <option value="<?= e($role) ?>" <?= $u['role'] === $role ? 'selected' : '' ?>><?= e(ucfirst($role)) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['pending', 'approved', 'rejected'] as $status): ?>
                            <option value="<?= e($status) ?>" <?= $u['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?= e($u['date_of_birth'] ?? '') ?>">

                    <label>Gender</label>
                    <select name="gender">
                        <option value="">Select</option>
                        <option value="Male" <?= ($u['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($u['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>

                    <label>Phone</label>
                    <input name="phone" value="<?= e($u['phone'] ?? '') ?>">

                    <label>Address</label>
                    <textarea name="address"><?= e($u['address'] ?? '') ?></textarea>
                </td>
                <td>
                    <button class="btn small" name="action" value="update" type="submit"><i class="bi bi-save"></i> Update</button>
                    <?php if ((int)$u['id'] !== (int)$user['id']): ?>
                        <button class="btn small danger" name="action" value="delete" type="submit" onclick="return confirm('Delete this user? This action cannot be undone.');"><i class="bi bi-trash"></i> Delete</button>
                    <?php endif; ?>
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<?php renderFooter(); ?>
