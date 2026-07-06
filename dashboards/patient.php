<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('patient');
$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM patients WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user['id']]);
$patientId = (int)$stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT mr.*, du.full_name AS doctor_name, nu.full_name AS nurse_name FROM medical_records mr LEFT JOIN users du ON mr.doctor_id = du.id LEFT JOIN users nu ON mr.nurse_id = nu.id WHERE mr.patient_id = :patient_id ORDER BY mr.created_at DESC");
$stmt->execute(['patient_id' => $patientId]);
$records = $stmt->fetchAll();
renderHeader('Patient Dashboard');
?>
<div class="dashboard-title"><h1>Patient Dashboard</h1><div style="display:flex;gap:.6rem;flex-wrap:wrap"><a class="btn small outline" href="/profile.php"><i class="bi bi-person-circle"></i> My Profile</a><span class="badge approved"><?= e($user['system_id']) ?></span></div></div>
<section class="grid cards" style="margin-bottom:1rem"><div class="card"><i class="bi bi-file-medical"></i><h3>My Records</h3><p><?= count($records) ?> record(s)</p></div><div class="card"><i class="bi bi-person-circle"></i><h3>My Profile</h3><p><a href="/profile.php">View and update your profile</a></p></div><div class="card"><i class="bi bi-shield-lock"></i><h3>Privacy</h3><p>You can only access your own health records.</p></div><div class="card"><i class="bi bi-clock-history"></i><h3>History</h3><p>Your medical history appears below.</p></div></section>
<h2>My Medical Records</h2><div class="table-wrap"><table><tr><th>Date</th><th>Doctor</th><th>Nurse</th><th>Diagnosis</th><th>Treatment</th><th>Vitals</th><th>Nursing Notes</th></tr><?php foreach ($records as $r): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['doctor_name'] ?? '') ?></td><td><?= e($r['nurse_name'] ?? '') ?></td><td><?= e($r['diagnosis'] ?? '') ?></td><td><?= e($r['treatment'] ?? '') ?></td><td><?= e($r['vital_signs'] ?? '') ?></td><td><?= e($r['nursing_notes'] ?? '') ?></td></tr><?php endforeach; ?><?php if (!$records): ?><tr><td colspan="7">No medical records have been added yet.</td></tr><?php endif; ?></table></div>
<?php renderFooter(); ?>
