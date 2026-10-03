<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('driver');
$user = currentUser();

$areaId = (int)($_GET['area'] ?? 0);
$areas = $pdo->query('SELECT * FROM parking_areas ORDER BY name')->fetchAll();
$slots = [];
$selectedArea = null;
if ($areaId) {
    $selectedArea = $pdo->prepare('SELECT * FROM parking_areas WHERE id = :id');
    $selectedArea->execute(['id' => $areaId]);
    $selectedArea = $selectedArea->fetch();
    $slots = $pdo->prepare('SELECT * FROM parking_slots WHERE area_id = :area AND status = "Available" ORDER BY slot_number');
    $slots->execute(['area' => $areaId]);
    $slots = $slots->fetchAll();
}

$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_id'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request token.';
    }
    $slotId = (int)$_POST['slot_id'];
    $date = sanitize($_POST['reservation_date']);
    $start = sanitize($_POST['start_time']);
    $end = sanitize($_POST['end_time']);
    if (!$slotId || !$date || !$start || !$end) {
        $errors[] = 'Please complete all fields.';
    }
    if (empty($errors)) {
        $slot = $pdo->prepare('SELECT p.*, a.price_per_hour FROM parking_slots p JOIN parking_areas a ON p.area_id=a.id WHERE p.id=:id');
        $slot->execute(['id' => $slotId]);
        $slot = $slot->fetch();
        if (!$slot) {
            $errors[] = 'Selected slot not found.';
        }
    }
    if (empty($errors)) {
        $startTime = date('Y-m-d H:i:s', strtotime("$date $start"));
        $endTime = date('Y-m-d H:i:s', strtotime("$date $end"));
        $durationHours = max(1, ceil((strtotime($endTime) - strtotime($startTime))/3600));
        $fee = $durationHours * $slot['price_per_hour'];
        $qrCode = bin2hex(random_bytes(8));

        $stmt = $pdo->prepare('INSERT INTO reservations (user_id, slot_id, qr_code, reservation_date, start_time, end_time, status, total_fee) VALUES (:user,:slot,:qr,:date,:start,:end,:status,:fee)');
        $stmt->execute(['user'=>$user['id'],'slot'=>$slotId,'qr'=>$qrCode,'date'=>$date,'start'=>$startTime,'end'=>$endTime,'status'=>'Reserved','fee'=>$fee]);
        $pdo->prepare('UPDATE parking_slots SET status = "Reserved" WHERE id = :id')->execute(['id' => $slotId]);
        $success = 'Reservation confirmed. Your QR code is available in My Reservations.';
    }
}

include __DIR__ . '/../includes/header.php';
?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<div class="content p-4">
    <?php include __DIR__ . '/../includes/navbar.php'; ?>
    <div class="container-fluid">
        <h1 class="h3 mb-3">Reserve a Slot</h1>
        <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-danger"><ul><?php foreach ($errors as $error): ?><li><?= sanitize($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5>Select Parking Area</h5>
                        <form method="get" action="reserve.php">
                            <div class="mb-3">
                                <select class="form-select" name="area" required onchange="this.form.submit()">
                                    <option value="">Choose area</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>" <?= $areaId === $area['id'] ? 'selected' : '' ?>><?= sanitize($area['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if ($selectedArea): ?>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5><?= sanitize($selectedArea['name']) ?></h5>
                            <p class="mb-1">Address: <?= sanitize($selectedArea['address']) ?></p>
                            <p class="mb-1">Rate: &#8369;<?= number_format($selectedArea['price_per_hour'], 2) ?>/hr</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Reservation Form</h5>
                        <form method="post" action="reserve.php">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <div class="mb-3">
                                <label class="form-label">Available Slot</label>
                                <select class="form-select" name="slot_id" required>
                                    <option value="">Choose a slot</option>
                                    <?php foreach ($slots as $slot): ?>
                                        <option value="<?= $slot['id'] ?>"><?= sanitize($slot['slot_number']) ?> (<?= sanitize($slot['slot_type']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Date</label><input type="date" name="reservation_date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
                                <div class="col-md-3"><label class="form-label">Start</label><input type="time" name="start_time" class="form-control" required></div>
                                <div class="col-md-3"><label class="form-label">End</label><input type="time" name="end_time" class="form-control" required></div>
                            </div>
                            <button type="submit" class="btn btn-success mt-4">Confirm Reservation</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
