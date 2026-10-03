<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$reservations = $pdo->query('SELECT r.*, u.full_name, p.slot_number, a.name AS area_name FROM reservations r JOIN users u ON r.user_id=u.id JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id ORDER BY r.created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Manage Reservations</h1>
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Driver</th><th>Area</th><th>Slot</th><th>Reservation Date</th><th>Status</th><th>Fee</th></tr></thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= sanitize($reservation['id']) ?></td>
                                <td><?= sanitize($reservation['full_name']) ?></td>
                                <td><?= sanitize($reservation['area_name']) ?></td>
                                <td><?= sanitize($reservation['slot_number']) ?></td>
                                <td><?= sanitize($reservation['reservation_date']) ?></td>
                                <td><span class="badge bg-<?= $reservation['status'] === 'Reserved' ? 'warning text-dark' : ($reservation['status']==='CheckedIn' ? 'danger' : ($reservation['status']==='Completed' ? 'success' : 'secondary')) ?>"><?= sanitize($reservation['status']) ?></span></td>
                                <td>&#8369;<?= number_format($reservation['total_fee'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
