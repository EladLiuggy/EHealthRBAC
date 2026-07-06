<?php
require_once __DIR__ . '/../app/bootstrap.php';
verifyCsrf();

// $captchaQuestion = createCaptcha();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db();

    $fullName = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $licenseNumber = trim($_POST['license_number'] ?? '');
    $captcha = $_POST['captcha'] ?? '';

    if (!verifyCaptchaAnswer($captcha)) {
        flash('error', 'Robot check failed. Please try again.');
        redirect('/register.php');
    }

    if (!$fullName || !$email || !$role || !$password) {
        flash('error', 'Please fill all required fields.');
        redirect('/register.php');
    }

    if (!in_array($role, ['patient', 'doctor', 'nurse'], true)) {
        flash('error', 'Invalid role selected.');
        redirect('/register.php');
    }

    if ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
        redirect('/register.php');
    }

    if (!strongPassword($password)) {
        flash('error', 'Password must be at least 8 characters and include uppercase, lowercase, and number.');
        redirect('/register.php');
    }

    if (in_array($role, ['doctor', 'nurse'], true) && !$licenseNumber) {
        flash('error', 'Doctors and nurses must enter license number.');
        redirect('/register.php');
    }

    $licenseFileName = null;

    if (in_array($role, ['doctor', 'nurse'], true)) {
        if (empty($_FILES['license_file']['name'])) {
            flash('error', 'Doctors and nurses must upload license photo/document.');
            redirect('/register.php');
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
        $mime = mime_content_type($_FILES['license_file']['tmp_name']);

        if (!isset($allowed[$mime])) {
            flash('error', 'License upload must be JPG, PNG, or PDF.');
            redirect('/register.php');
        }

        if ($_FILES['license_file']['size'] > 2 * 1024 * 1024) {
            flash('error', 'License file must not exceed 2MB.');
            redirect('/register.php');
        }

        $licenseFileName = uniqid('license_', true) . '.' . $allowed[$mime];
        move_uploaded_file($_FILES['license_file']['tmp_name'], UPLOAD_DIR . $licenseFileName);
    }

    try {
        $pdo->beginTransaction();

        $systemId = generateSystemId($role, $pdo);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $status = $role === 'patient' ? 'approved' : 'pending';

        if (in_array($role, ['doctor', 'nurse'], true)) {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM license_registry
                WHERE license_number = :license AND staff_type = :role AND status = 'valid'
            ");
            $stmt->execute(['license' => $licenseNumber, 'role' => $role]);
            if ((int)$stmt->fetchColumn() > 0) {
                $status = 'approved';
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO users (system_id, full_name, email, password_hash, role, status)
            VALUES (:system_id, :full_name, :email, :password_hash, :role, :status)
            RETURNING id
        ");
        $stmt->execute([
            'system_id' => $systemId,
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role,
            'status' => $status
        ]);
        $userId = (int)$stmt->fetchColumn();

        if ($role === 'patient') {
            $stmt = $pdo->prepare("
                INSERT INTO patients (user_id, date_of_birth, gender, phone, address)
                VALUES (:user_id, :dob, :gender, :phone, :address)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'dob' => $_POST['date_of_birth'] ?: null,
                'gender' => $_POST['gender'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'address' => $_POST['address'] ?? null
            ]);
        }

        if (in_array($role, ['doctor', 'nurse'], true)) {
            $method = $status === 'approved' ? 'automatic' : 'manual';
            $stmt = $pdo->prepare("
                INSERT INTO staff_verifications
                (user_id, license_number, license_file, verification_method, verification_status)
                VALUES (:user_id, :license_number, :license_file, :method, :status)
            ");
            $stmt->execute([
                'user_id' => $userId,
                'license_number' => $licenseNumber,
                'license_file' => $licenseFileName,
                'method' => $method,
                'status' => $status
            ]);
        }

        logAction($userId, "Registered new {$role} account with system ID {$systemId}");
        $pdo->commit();

        flash('success', "Account created successfully. Your ID is {$systemId}.");
        redirect('/login.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash('error', 'Registration failed. Email or license number may already exist.');
        redirect('/register.php');
    }
}

$captchaQuestion = createCaptcha();
renderHeader('Create Account');
?>
<div class="form-card">
    <h1>Create Account</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Full Name</label>
        <input name="full_name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Role</label>
        <select name="role" id="role" required>
            <option value="">Select role</option>
            <option value="patient">Patient</option>
            <option value="doctor">Doctor</option>
            <option value="nurse">Nurse</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <label>Date of Birth - patients only</label>
        <input type="date" name="date_of_birth">

        <label>Gender - patients only</label>
        <select name="gender"><option value="">Select</option><option>Male</option><option>Female</option></select>

        <label>Phone</label>
        <input name="phone">

        <label>Address</label>
        <textarea name="address"></textarea>

        <label>License Number - doctors/nurses only</label>
        <input name="license_number">

        <label>License Photo/PDF - doctors/nurses only</label>
        <input type="file" name="license_file">

        <label><?= e($captchaQuestion) ?></label>
        <input name="captcha" required>

        <button type="submit" style="margin-top:1.2rem">Create Account</button>
    </form>
</div>
<?php renderFooter(); ?>
