<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('guard');

$activeVehicles = $pdo->query("SELECT r.*, u.full_name, p.slot_number, a.name AS area_name FROM reservations r JOIN users u ON r.user_id=u.id JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id WHERE r.status = 'CheckedIn' ORDER BY r.check_in_time DESC")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Guard Dashboard</h1>
        <div class="row g-3">
            <div class="col-md-4"><div class="card shadow-sm border-success h-100"><div class="card-body"><h6>Active Vehicles</h6><h3><?= count($activeVehicles) ?></h3></div></div></div>
            <div class="col-md-4"><div class="card shadow-sm border-primary h-100"><div class="card-body"><h6>Scan Entry</h6><p>Validate reservation at entry gate.</p></div></div></div>
            <div class="col-md-4"><div class="card shadow-sm border-warning h-100"><div class="card-body"><h6>Scan Exit</h6><p>Process check-out and fees.</p></div></div></div>
        </div>
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <h5>Active Vehicles</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>#</th><th>Driver</th><th>Area</th><th>Slot</th><th>Check-In</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach ($activeVehicles as $vehicle): ?>
                                <tr>
                                    <td><?= sanitize($vehicle['id']) ?></td>
                                    <td><?= sanitize($vehicle['full_name']) ?></td>
                                    <td><?= sanitize($vehicle['area_name']) ?></td>
                                    <td><?= sanitize($vehicle['slot_number']) ?></td>
                                    <td><?= sanitize($vehicle['check_in_time']) ?></td>
                                    <td><span class="badge bg-danger">Checked In</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
