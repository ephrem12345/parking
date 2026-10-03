<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');
$area = (int)($_GET['area'] ?? 0);
$date = sanitize($_GET['date'] ?? date('Y-m-d'));
if (!$area) {
    echo json_encode(['error' => 'Area is required']);
    exit;
}
$slots = $pdo->prepare('SELECT * FROM parking_slots WHERE area_id=:area AND status = "Available" ORDER BY slot_number');
$slots->execute(['area' => $area]);
$available = $slots->fetchAll();

echo json_encode(['available_slots' => $available, 'count' => count($available), 'date' => $date]);
