<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');
$reservationId = (int)($_GET['reservation_id'] ?? 0);
if (!$reservationId) {
    echo json_encode(['error' => 'Reservation ID required']);
    exit;
}
$stmt = $pdo->prepare('SELECT r.*, a.price_per_hour FROM reservations r JOIN parking_slots p ON r.slot_id=p.id JOIN parking_areas a ON p.area_id=a.id WHERE r.id=:id');
$stmt->execute(['id' => $reservationId]);
$reservation = $stmt->fetch();
if (!$reservation) {
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}
$checkout = isset($_GET['checkout']) ? sanitize($_GET['checkout']) : date('Y-m-d H:i:s');
$duration = max(1, ceil((strtotime($checkout) - strtotime($reservation['check_in_time'])) / 3600));
$total = $duration * $reservation['price_per_hour'];

echo json_encode(['duration_hours' => $duration, 'total_fee' => $total]);
