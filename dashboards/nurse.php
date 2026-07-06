<?php
require_once __DIR__ . '/../app/bootstrap.php';
$user = requireRole('nurse');
$pdo = db();
$stmt = $pdo->prepare("SELECT p.id AS patient_id, u.full_name, u.system_id, p.gender, p.phone FROM patient_assignments pa JOIN patients p ON pa.patient_id = p.id JOIN users u ON p.user_id = u.id WHERE pa.nurse_id = :nurse_id ORDER BY u.full_name");
$stmt->execute(['nurse_id' => $user['id']]);
$patients = $stmt->fetchAll();
renderHeader('Nurse Dashboard');
?>
<div class="dashboard-title"><h1>Nurse Dashboard</h1><div style="display:flex;gap:.6rem;flex-wrap:wrap"><a class="btn small outline" href="/profile.php"><i class="bi bi-person-circle"></i> My Profile</a><span class="badge approved"><?= e($user['system_id']) ?></span></div></div>
<section class="grid cards" style="margin-bottom:1rem"><div class="card"><i class="bi bi-people"></i><h3>Assigned Patients</h3><p><?= count($patients) ?></p></div><div class="card"><i class="bi bi-heart-pulse"></i><h3>Vitals</h3><p>Record vital signs and nursing notes.</p></div><div class="card"><i class="bi bi-clipboard2-pulse"></i><h3>Medical Records</h3><p>View assigned patient records.</p></div><div class="card"><i class="bi bi-person-circle"></i><h3>Profile</h3><p><a href="/profile.php">View and update your profile</a></p></div></section>
<h2>Assigned Patients</h2><div class="table-wrap"><table><tr><th>Patient ID</th><th>Name</th><th>Gender</th><th>Phone</th><th>Actions</th></tr><?php foreach ($patients as $p): ?><tr><td><?= e($p['system_id']) ?></td><td><?= e($p['full_name']) ?></td><td><?= e($p['gender'] ?? '') ?></td><td><?= e($p['phone'] ?? '') ?></td><td style="display:flex;gap:.5rem;flex-wrap:wrap"><a class="btn small outline" href="/records/history.php?patient_id=<?= (int)$p['patient_id'] ?>"><i class="bi bi-clock-history"></i> View Records</a><a class="btn small" href="/records/nurse_update.php?patient_id=<?= (int)$p['patient_id'] ?>"><i class="bi bi-plus-circle"></i> Add Vitals/Notes</a></td></tr><?php endforeach; ?><?php if (!$patients): ?><tr><td colspan="5">No patient has been assigned to you yet.</td></tr><?php endif; ?></table></div>
<?php renderFooter(); ?>
