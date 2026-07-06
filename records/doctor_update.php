<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('doctor');
$pdo = db();
verifyCsrf();

$patientId = (int)($_GET['patient_id'] ?? $_POST['patient_id'] ?? 0);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM patient_assignments WHERE patient_id = :pid AND doctor_id = :did");
$stmt->execute(['pid' => $patientId, 'did' => $user['id']]);
if ((int)$stmt->fetchColumn() === 0) {
    http_response_code(403);
    require __DIR__ . '/../public/403.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $treatment = trim($_POST['treatment'] ?? '');

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, updated_at)
        VALUES (:patient_id, :doctor_id, :diagnosis, :treatment, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([
        'patient_id' => $patientId,
        'doctor_id' => $user['id'],
        'diagnosis' => $diagnosis,
        'treatment' => $treatment
    ]);

    logAction((int)$user['id'], "Doctor updated diagnosis/treatment for patient {$patientId}");
    flash('success', 'Patient record updated.');
    redirect('/dashboards/doctor.php');
}

$stmt = $pdo->prepare("
    SELECT u.full_name, u.system_id
    FROM patients p JOIN users u ON p.user_id = u.id
    WHERE p.id = :id
");
$stmt->execute(['id' => $patientId]);
$patient = $stmt->fetch();

renderHeader('Update Patient Record');
?>
<div class="form-card">
    <h1>Update Record</h1>
    <p><strong><?= e($patient['system_id'] . ' - ' . $patient['full_name']) ?></strong></p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="patient_id" value="<?= $patientId ?>">
        <label>Diagnosis</label>
        <textarea name="diagnosis" required></textarea>
        <label>Treatment</label>
        <textarea name="treatment" required></textarea>
        <button style="margin-top:1rem">Save Record</button>
    </form>
</div>
<?php renderFooter(); ?>
