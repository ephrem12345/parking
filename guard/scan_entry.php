<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('guard');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['qr_value'])) {
    $qrValue = sanitize($_POST['qr_value']);
    $stmt = $pdo->prepare('SELECT r.*, p.status AS slot_status FROM reservations r JOIN parking_slots p ON r.slot_id=p.id WHERE r.qr_code=:qr LIMIT 1');
    $stmt->execute(['qr' => $qrValue]);
    $reservation = $stmt->fetch();
    if ($reservation && $reservation['status'] === 'Reserved') {
        $pdo->beginTransaction();
        $pdo->prepare('UPDATE reservations SET status = "CheckedIn", check_in_time = NOW() WHERE id = :id')->execute(['id' => $reservation['id']]);
        $pdo->prepare('UPDATE parking_slots SET status = "Occupied" WHERE id = :slot')->execute(['slot' => $reservation['slot_id']]);
        $pdo->commit();
        $message = 'Entry validated. Vehicle marked as occupied.';
    } else {
        $message = 'Reservation is invalid or already checked in.';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Scan Entry</h1>
        <?php if ($message): ?><div class="alert alert-info"><?= sanitize($message) ?></div><?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div id="entryScanner" class="border rounded p-3 bg-white"></div>
            </div>
            <div class="col-md-6">
                <form method="post" action="scan_entry.php">
                    <input name="qr_value" id="entryQr" type="text" class="form-control" placeholder="QR value detected" required>
                    <button type="submit" class="btn btn-success mt-3">Validate Entry</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $customScripts = "<script>const entryScanner = new Html5Qrcode('entryScanner');function onEntryScanSuccess(decoded){document.getElementById('entryQr').value = decoded; entryScanner.stop().catch(()=>{});}; function onEntryScanError(err){}; entryScanner.start({facingMode:'environment'}, {fps:10, qrbox:250}, onEntryScanSuccess, onEntryScanError);</script>"; include __DIR__ . '/../includes/footer.php'; ?>
