<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');
$user = currentUser();
$reservations = $pdo->prepare('SELECT r.*, p.slot_number, a.name AS area_name FROM reservations r JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id WHERE r.user_id=:user ORDER BY r.created_at DESC');
$reservations->execute(['user'=>$user['id']]);
$reservations = $reservations->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">My Reservations</h1>
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Area</th><th>Slot</th><th>Date</th><th>From</th><th>To</th><th>Status</th><th>Fee</th><th>QR Code</th></tr></thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= sanitize($reservation['id']) ?></td>
                                <td><?= sanitize($reservation['area_name']) ?></td>
                                <td><?= sanitize($reservation['slot_number']) ?></td>
                                <td><?= sanitize($reservation['reservation_date']) ?></td>
                                <td><?= sanitize($reservation['start_time']) ?></td>
                                <td><?= sanitize($reservation['end_time']) ?></td>
                                <td><span class="badge bg-<?= $reservation['status'] === 'Reserved' ? 'warning text-dark' : ($reservation['status']==='CheckedIn' ? 'danger' : ($reservation['status']==='Completed' ? 'success' : 'secondary')) ?>"><?= sanitize($reservation['status']) ?></span></td>
                                <td>&#8369;<?= number_format($reservation['total_fee'], 2) ?></td>
                                <td><button class="btn btn-sm btn-outline-primary" onclick="showQr('<?= sanitize($reservation['qr_code']) ?>')">View</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="qrModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Reservation QR Code</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body text-center"><div id="qrTarget"></div><p class="mt-3" id="qrText"></p></div></div></div></div>
<?php $customScripts = "<script>function showQr(value){document.getElementById('qrTarget').innerHTML=''; new QRCode(document.getElementById('qrTarget'), {text:value,width:200,height:200}); document.getElementById('qrText').textContent=value; new bootstrap.Modal(document.getElementById('qrModal')).show();}</script>"; include __DIR__ . '/../includes/footer.php'; ?>
