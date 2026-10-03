<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('guard');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['qr_value'])) {
    $qrValue = sanitize($_POST['qr_value']);
    $stmt = $pdo->prepare('SELECT r.*, a.price_per_hour FROM reservations r JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id WHERE r.qr_code=:qr LIMIT 1');
    $stmt->execute(['qr' => $qrValue]);
    $reservation = $stmt->fetch();
    if ($reservation && $reservation['status'] === 'CheckedIn') {
        $checkOut = date('Y-m-d H:i:s');
        $duration = max(1, ceil((strtotime($checkOut) - strtotime($reservation['check_in_time'])) / 3600));
        $fee = $duration * $reservation['price_per_hour'];
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE reservations SET status = "Completed", check_out_time = :out, total_fee = :fee WHERE id = :id')->execute(['out'=>$checkOut,'fee'=>$fee,'id'=>$reservation['id']]);
        $pdo->prepare('UPDATE parking_slots SET status = "Available" WHERE id = :slot')->execute(['slot' => $reservation['slot_id']]);
        $pdo->prepare('INSERT INTO payments (reservation_id, amount, payment_method, transaction_reference, status) VALUES (:res,:amount,:method,:ref,:status)')->execute(['res'=>$reservation['id'],'amount'=>$fee,'method'=>'Cash','ref'=>uniqid('TXN-'),'status'=>'Paid']);
        $pdo->commit();
        $message = 'Exit processed successfully. Fee: &#8369;' . number_format($fee,2);
    } else {
        $message = 'Invalid reservation or vehicle not checked in.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Scan Exit</h1>
        <?php if ($message): ?><div class="alert alert-info"><?= $message ?></div><?php endif; ?>
        <div class="row">
            <div class="col-md-6"><div id="exitScanner" class="border rounded p-3 bg-white"></div></div>
            <div class="col-md-6">
                <form method="post" action="scan_exit.php">
                    <input name="qr_value" id="exitQr" type="text" class="form-control" placeholder="QR value detected" required>
                    <button type="submit" class="btn btn-warning mt-3">Process Exit</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $customScripts = "<script>const exitScanner = new Html5Qrcode('exitScanner');function onExitScanSuccess(decoded){document.getElementById('exitQr').value = decoded; exitScanner.stop().catch(()=>{});} function onExitScanError(err){} exitScanner.start({facingMode:'environment'},{fps:10,qrbox:250},onExitScanSuccess,onExitScanError);</script>"; include __DIR__ . '/../includes/footer.php'; ?>
