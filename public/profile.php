<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireLogin();
$pdo = db();
verifyCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?: null;
    $gender = $_POST['gender'] ?: null;

    if (!$fullName || !$email) {
        flash('error', 'Full name and email are required.');
        redirect('/profile.php');
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            UPDATE users
            SET full_name = :full_name,
                email = :email,
                phone = :phone,
                address = :address,
                date_of_birth = :date_of_birth,
                gender = :gender
            WHERE id = :id
        ");
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'id' => $user['id']
        ]);

        if ($user['role'] === 'patient') {
            $stmt = $pdo->prepare("
                UPDATE patients
                SET date_of_birth = :dob,
                    gender = :gender,
                    phone = :phone,
                    address = :address
                WHERE user_id = :user_id
            ");
            $stmt->execute([
                'dob' => $dateOfBirth,
                'gender' => $gender,
                'phone' => $phone,
                'address' => $address,
                'user_id' => $user['id']
            ]);
        }

        logAction((int)$user['id'], 'Updated profile');
        $pdo->commit();

        flash('success', 'Profile updated successfully.');
        redirect('/profile.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('error', 'Profile update failed. The email may already be in use, or database update may not have been applied.');
        redirect('/profile.php');
    }
}

$patient = null;
$verification = null;

if ($user['role'] === 'patient') {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $patient = $stmt->fetch();
}

if (in_array($user['role'], ['doctor', 'nurse'], true)) {
    $stmt = $pdo->prepare("SELECT * FROM staff_verifications WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user['id']]);
    $verification = $stmt->fetch();
}

$profilePhone = $user['phone'] ?? ($patient['phone'] ?? '');
$profileAddress = $user['address'] ?? ($patient['address'] ?? '');
$profileDob = $user['date_of_birth'] ?? ($patient['date_of_birth'] ?? '');
$profileGender = $user['gender'] ?? ($patient['gender'] ?? '');

renderHeader('My Profile');
?>
<div class="form-card">
    <h1>My Profile</h1>
    <p><strong>System ID:</strong> <?= e($user['system_id']) ?></p>
    <p><strong>Role:</strong> <?= e(ucfirst($user['role'])) ?></p>
    <p><strong>Status:</strong> <span class="badge <?= e($user['status']) ?>"><?= e($user['status']) ?></span></p>

    <?php if ($verification): ?>
        <p><strong>License Number:</strong> <?= e($verification['license_number']) ?></p>
        <p><strong>Verification Method:</strong> <?= e($verification['verification_method']) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Full Name</label>
        <input name="full_name" value="<?= e($user['full_name']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($user['email']) ?>" required>

        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= e($profileDob ?? '') ?>">

        <label>Gender</label>
        <select name="gender">
            <option value="">Select</option>
            <option value="Male" <?= ($profileGender ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($profileGender ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
        </select>

        <label>Phone Number</label>
        <input name="phone" value="<?= e($profilePhone ?? '') ?>">

        <label>Address</label>
        <textarea name="address"><?= e($profileAddress ?? '') ?></textarea>

        <button type="submit" style="margin-top:1rem"><i class="bi bi-save"></i> Update Profile</button>
    </form>
</div>
<?php renderFooter(); ?>
