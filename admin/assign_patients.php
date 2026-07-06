<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('admin');
$pdo = db();
verifyCsrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = (int)($_POST['patient_id'] ?? 0);
    $doctorId = $_POST['doctor_id'] ? (int)$_POST['doctor_id'] : null;
    $nurseId = $_POST['nurse_id'] ? (int)$_POST['nurse_id'] : null;

    if (!$patientId || (!$doctorId && !$nurseId)) {
        flash('error', 'Select a patient and at least one staff member.');
        redirect('/admin/assign_patients.php');
    }

    $stmt = $pdo->prepare("
        INSERT INTO patient_assignments (patient_id, doctor_id, nurse_id, assigned_by)
        VALUES (:patient_id, :doctor_id, :nurse_id, :assigned_by)
    ");
    $stmt->execute([
        'patient_id' => $patientId,
        'doctor_id' => $doctorId,
        'nurse_id' => $nurseId,
        'assigned_by' => $user['id']
    ]);

    logAction((int)$user['id'], "Assigned patient {$patientId} to doctor {$doctorId} and nurse {$nurseId}");
    flash('success', 'Patient assigned successfully.');
    redirect('/admin/assign_patients.php');
}

$patients = $pdo->query("
    SELECT p.id, u.full_name, u.system_id
    FROM patients p JOIN users u ON p.user_id = u.id
    ORDER BY u.full_name
")->fetchAll();

$doctors = $pdo->query("SELECT id, full_name, system_id FROM users WHERE role='doctor' AND status='approved' ORDER BY full_name")->fetchAll();
$nurses = $pdo->query("SELECT id, full_name, system_id FROM users WHERE role='nurse' AND status='approved' ORDER BY full_name")->fetchAll();

renderHeader('Assign Patients');
?>
<div class="form-card">
    <h1>Assign Patient</h1>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Patient</label>
        <select name="patient_id" required>
            <option value="">Select patient</option>
            <?php foreach ($patients as $p): ?>
                <option value="<?= (int)$p['id'] ?>"><?= e($p['system_id'] . ' - ' . $p['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Doctor</label>
        <select name="doctor_id">
            <option value="">Select doctor</option>
            <?php foreach ($doctors as $d): ?>
                <option value="<?= (int)$d['id'] ?>"><?= e($d['system_id'] . ' - ' . $d['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Nurse</label>
        <select name="nurse_id">
            <option value="">Select nurse</option>
            <?php foreach ($nurses as $n): ?>
                <option value="<?= (int)$n['id'] ?>"><?= e($n['system_id'] . ' - ' . $n['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button style="margin-top:1rem">Assign</button>
    </form>
</div>
<?php renderFooter(); ?>
