<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');
$user = currentUser();

$stmt = $pdo->prepare('SELECT p.*, r.reservation_date, p2.slot_number FROM payments p JOIN reservations r ON p.reservation_id=r.id JOIN parking_slots p2 ON r.slot_id=p2.id WHERE r.user_id=:user ORDER BY p.paid_at DESC');
$stmt->execute(['user'=>$user['id']]);
$payments = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Payment History</h1>
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>#</th><th>Reservation Date</th><th>Slot</th><th>Amount</th><th>Method</th><th>Status</th><th>Paid At</th></tr></thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= sanitize($payment['id']) ?></td>
                            <td><?= sanitize($payment['reservation_date']) ?></td>
                            <td><?= sanitize($payment['slot_number']) ?></td>
                            <td>&#8369;<?= number_format($payment['amount'],2) ?></td>
                            <td><?= sanitize($payment['payment_method']) ?></td>
                            <td><span class="badge bg-<?= $payment['status']==='Paid' ? 'success' : 'warning text-dark' ?>"><?= sanitize($payment['status']) ?></span></td>
                            <td><?= sanitize($payment['paid_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
