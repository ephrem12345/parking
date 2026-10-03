<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$stmt = $pdo->query('SELECT COUNT(*) AS total FROM users');
$totalUsers = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM parking_areas');
$totalAreas = $stmt->fetchColumn();
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM parking_slots');
$totalSlots = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='Available'");
$availableSlots = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='Reserved'");
$reservedSlots = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM parking_slots WHERE status='Occupied'");
$occupiedSlots = $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT SUM(amount) FROM payments WHERE paid_at >= :start');
$stmt->execute(['start' => date('Y-m-d 00:00:00')]);
$todayRevenue = $stmt->fetchColumn() ?: 0;
$stmt = $pdo->prepare('SELECT SUM(amount) FROM payments WHERE paid_at >= :start');
$stmt->execute(['start' => date('Y-m-01 00:00:00')]);
$monthlyRevenue = $stmt->fetchColumn() ?: 0;

$activeChart = json_encode([$availableSlots, $reservedSlots, $occupiedSlots]);

include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h1 class="h3">Admin Dashboard</h1>
                        <p class="text-muted">Overview of parking operations and occupancy.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-success h-100">
                    <div class="card-body">
                        <h5>Total Users</h5>
                        <h2><?= (int)$totalUsers ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-primary h-100">
                    <div class="card-body">
                        <h5>Parking Areas</h5>
                        <h2><?= (int)$totalAreas ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-secondary h-100">
                    <div class="card-body">
                        <h5>Total Slots</h5>
                        <h2><?= (int)$totalSlots ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-body">
                        <h5>Today's Revenue</h5>
                        <h2>&#8369;<?= number_format($todayRevenue, 2) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5>Slot Status</h5>
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5>Key statuses</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">Available <span class="badge bg-success"><?= (int)$availableSlots ?></span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Reserved <span class="badge bg-warning"><?= (int)$reservedSlots ?></span></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Occupied <span class="badge bg-danger"><?= (int)$occupiedSlots ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5>Monthly Revenue</h5>
                        <p class="display-6 mb-0">&#8369;<?= number_format($monthlyRevenue, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $customScripts = "<script>const statusChart = new Chart(document.getElementById('statusChart'), {type:'doughnut', data:{labels:['Available','Reserved','Occupied'], datasets:[{label:'Slots', data:{$activeChart}, backgroundColor:['#28a745','#ffc107','#dc3545']} ]}});</script>"; include __DIR__ . '/../includes/footer.php'; ?>
