<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');
$userId = currentUser()['id'];
$notifications = $pdo->prepare('SELECT * FROM notifications WHERE user_id = :user ORDER BY created_at DESC LIMIT 10');
$notifications->execute(['user' => $userId]);
$items = $notifications->fetchAll();

echo json_encode(['items' => $items]);
