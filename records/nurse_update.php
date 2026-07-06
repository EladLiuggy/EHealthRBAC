<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('nurse');
$pdo = db();
verifyCsrf();

$patientId = (int)($_GET['patient_id'] ?? $_POST['patient_id'] ?? 0);

$stmt = $pdo->prepare("SELECT COUNT(*) FROM patient_assignments WHERE patient_id = :pid AND nurse_id = :nid");
$stmt->execute(['pid' => $patientId, 'nid' => $user['id']]);
if ((int)$stmt->fetchColumn() === 0) {
    http_response_code(403);
    require __DIR__ . '/../public/403.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vitals = trim($_POST['vital_signs'] ?? '');
    $notes = trim($_POST['nursing_notes'] ?? '');

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (patient_id, nurse_id, vital_signs, nursing_notes, updated_at)
        VALUES (:patient_id, :nurse_id, :vital_signs, :nursing_notes, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([
        'patient_id' => $patientId,
        'nurse_id' => $user['id'],
        'vital_signs' => $vitals,
        'nursing_notes' => $notes
    ]);

    logAction((int)$user['id'], "Nurse added vitals/notes for patient {$patientId}");
    flash('success', 'Vitals and nursing notes saved.');
    redirect('/dashboards/nurse.php');
}

$stmt = $pdo->prepare("
    SELECT u.full_name, u.system_id
    FROM patients p JOIN users u ON p.user_id = u.id
    WHERE p.id = :id
");
$stmt->execute(['id' => $patientId]);
$patient = $stmt->fetch();

renderHeader('Add Vitals and Notes');
?>
<div class="form-card">
    <h1>Add Vitals / Nursing Notes</h1>
    <p><strong><?= e($patient['system_id'] . ' - ' . $patient['full_name']) ?></strong></p>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="patient_id" value="<?= $patientId ?>">
        <label>Vital Signs</label>
        <textarea name="vital_signs" required placeholder="BP: 120/80, Temp: 37C, Pulse: 80 bpm"></textarea>
        <label>Nursing Notes</label>
        <textarea name="nursing_notes" required></textarea>
        <button style="margin-top:1rem">Save Notes</button>
    </form>
</div>
<?php renderFooter(); ?>
