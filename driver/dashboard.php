<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');
$user = currentUser();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE user_id = :user');
$stmt->execute(['user' => $user['id']]);
$totalReservations = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user AND status='Reserved'");
$stmt->execute(['user' => $user['id']]);
$reservedCount = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = :user AND status='Completed'");
$stmt->execute(['user' => $user['id']]);
$completedCount = $stmt->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <div class="row gy-3">
            <div class="col-12">
                <h1 class="h3">Driver Dashboard</h1>
                <p class="text-muted">Track your reservations, payments, and parking activity.</p>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-success h-100"><div class="card-body"><h6>Total Reservations</h6><h3><?= (int)$totalReservations ?></h3></div></div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-warning h-100"><div class="card-body"><h6>Active Reservations</h6><h3><?= (int)$reservedCount ?></h3></div></div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-primary h-100"><div class="card-body"><h6>Completed Trips</h6><h3><?= (int)$completedCount ?></h3></div></div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Quick Actions</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-success" href="parking_areas.php">View Areas</a>
                            <a class="btn btn-primary" href="reserve.php">Reserve Slot</a>
                            <a class="btn btn-outline-secondary" href="my_reservations.php">My Reservations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
