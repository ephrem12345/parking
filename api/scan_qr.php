<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');
$qr = sanitize($_GET['qr'] ?? '');
if (!$qr) {
    echo json_encode(['error' => 'QR code is required']);
    exit;
}
$stmt = $pdo->prepare('SELECT r.*, p.slot_number, a.name AS area_name FROM reservations r JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id WHERE r.qr_code=:qr LIMIT 1');
$stmt->execute(['qr' => $qr]);
$reservation = $stmt->fetch();
if (!$reservation) {
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}

echo json_encode(['reservation' => $reservation]);
