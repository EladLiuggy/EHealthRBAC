<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireLogin();
$pdo = db();
$patientId = (int)($_GET['patient_id'] ?? 0);
if (!$patientId) { flash('error', 'Patient not selected.'); redirect(dashboardPath($user['role'])); }
$allowed = false;
if ($user['role'] === 'admin') { $allowed = true; }
elseif ($user['role'] === 'doctor') { $stmt = $pdo->prepare("SELECT COUNT(*) FROM patient_assignments WHERE patient_id = :pid AND doctor_id = :uid"); $stmt->execute(['pid'=>$patientId,'uid'=>$user['id']]); $allowed = (int)$stmt->fetchColumn() > 0; }
elseif ($user['role'] === 'nurse') { $stmt = $pdo->prepare("SELECT COUNT(*) FROM patient_assignments WHERE patient_id = :pid AND nurse_id = :uid"); $stmt->execute(['pid'=>$patientId,'uid'=>$user['id']]); $allowed = (int)$stmt->fetchColumn() > 0; }
elseif ($user['role'] === 'patient') { $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE id = :pid AND user_id = :uid"); $stmt->execute(['pid'=>$patientId,'uid'=>$user['id']]); $allowed = (int)$stmt->fetchColumn() > 0; }
if (!$allowed) { http_response_code(403); require __DIR__ . '/../public/403.php'; exit; }
$stmt = $pdo->prepare("SELECT p.id, pu.full_name, pu.system_id, p.gender, p.phone FROM patients p JOIN users pu ON p.user_id = pu.id WHERE p.id = :id");
$stmt->execute(['id'=>$patientId]);
$patient = $stmt->fetch();
$stmt = $pdo->prepare("SELECT mr.*, du.full_name AS doctor_name, nu.full_name AS nurse_name FROM medical_records mr LEFT JOIN users du ON mr.doctor_id = du.id LEFT JOIN users nu ON mr.nurse_id = nu.id WHERE mr.patient_id = :patient_id ORDER BY mr.created_at DESC");
$stmt->execute(['patient_id'=>$patientId]);
$records = $stmt->fetchAll();
logAction((int)$user['id'], "Viewed medical history for patient {$patientId}");
renderHeader('Patient Medical History');
?>
<div class="dashboard-title"><div><h1>Patient Medical History</h1><p><strong><?= e($patient['system_id'] . ' - ' . $patient['full_name']) ?></strong></p></div><a class="btn small outline" href="<?= dashboardPath($user['role']) ?>"><i class="bi bi-arrow-left"></i> Back</a></div>
<div class="table-wrap"><table><tr><th>Date</th><th>Doctor</th><th>Nurse</th><th>Diagnosis</th><th>Treatment</th><th>Vital Signs</th><th>Nursing Notes</th></tr><?php foreach ($records as $r): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['doctor_name'] ?? '') ?></td><td><?= e($r['nurse_name'] ?? '') ?></td><td><?= e($r['diagnosis'] ?? '') ?></td><td><?= e($r['treatment'] ?? '') ?></td><td><?= e($r['vital_signs'] ?? '') ?></td><td><?= e($r['nursing_notes'] ?? '') ?></td></tr><?php endforeach; ?><?php if (!$records): ?><tr><td colspan="7">No medical history has been recorded for this patient yet.</td></tr><?php endif; ?></table></div>
<?php renderFooter(); ?>
