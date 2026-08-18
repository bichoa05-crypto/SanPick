<?php
// Copy this file to config/db.php and adjust credentials if needed.
$DB_HOST = '127.0.0.1';
$DB_NAME = 'datlichthethao';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default: root with empty password

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB Connection failed: ' . $e->getMessage());
}

// If your project uses mysqli, you can instead use:
// $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
// if ($conn->connect_error) { die('Connect error: '.$conn->connect_error); }

?>