<?php
// Database connection with PDO
$config = require __DIR__ . '/config.php';

date_default_timezone_set($config['timezone']);

try {
    $dbHost = 'localhost';
    $dbName = 'parking_system';
    $dbUser = 'root';
    $dbPass = '';

    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
