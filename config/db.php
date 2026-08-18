<?php
// DB config -- prefer environment variables (for Render, Docker, XAMPP fallback)
$servername = getenv('DB_HOST') ?: '127.0.0.1';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname   = getenv('DB_NAME') ?: 'datlichthethao';
$port     = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, (int)$port);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>