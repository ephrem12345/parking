<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}
$slotId = (int)($payload['slot_id'] ?? 0);
$date = sanitize($payload['reservation_date'] ?? '');
$start = sanitize($payload['start_time'] ?? '');
$end = sanitize($payload['end_time'] ?? '');
if (!$slotId || !$date || !$start || !$end) {
    echo json_encode(['error' => 'Missing required reservation parameters']);
    exit;
}
$stmt = $pdo->prepare('SELECT p.*, a.price_per_hour FROM parking_slots p JOIN parking_areas a ON p.area_id=a.id WHERE p.id=:id AND p.status = "Available"');
$stmt->execute(['id' => $slotId]);
$slot = $stmt->fetch();
if (!$slot) {
    echo json_encode(['error' => 'Slot not available']);
    exit;
}
$startTime = date('Y-m-d H:i:s', strtotime("$date $start"));
$endTime = date('Y-m-d H:i:s', strtotime("$date $end"));
$durationHours = max(1, ceil((strtotime($endTime) - strtotime($startTime))/3600));
$fee = $durationHours * $slot['price_per_hour'];
$qrCode = bin2hex(random_bytes(8));
$pdo->beginTransaction();
$pdo->prepare('INSERT INTO reservations (user_id, slot_id, qr_code, reservation_date, start_time, end_time, status, total_fee) VALUES (:user,:slot,:qr,:date,:start,:end,:status,:fee)')->execute([
    'user' => currentUser()['id'],
    'slot' => $slotId,
    'qr' => $qrCode,
    'date' => $date,
    'start' => $startTime,
    'end' => $endTime,
    'status' => 'Reserved',
    'fee' => $fee,
]);
$pdo->prepare('UPDATE parking_slots SET status = "Reserved" WHERE id = :id')->execute(['id' => $slotId]);
$pdo->commit();

echo json_encode(['success' => true, 'message' => 'Reservation created', 'qr_code' => $qrCode, 'fee' => $fee]);
