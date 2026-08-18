<?php
header('Content-Type: application/json');
require_once '../config/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM users WHERE user_id = $id LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(null);
    }
    exit; 
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT user_id, ho_ten, email, sdt, ngay_tao FROM users WHERE vai_tro = 'khach_hang'";

if (!empty($search)) {
    $sql .= " AND (ho_ten LIKE '%$search%' OR sdt LIKE '%$search%' OR email LIKE '%$search%')";
}

$sql .= " ORDER BY ngay_tao DESC";
$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>