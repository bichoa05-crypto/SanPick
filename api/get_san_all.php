<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (isset($_GET['khu_vuc_id'])) {
    $kvId = intval($_GET['khu_vuc_id']);
    $stmt = $conn->prepare("SELECT s.san_id, s.khu_vuc_id, s.ten_san, s.trang_thai, kv.ten_kv 
                            FROM san s JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id 
                            WHERE s.khu_vuc_id = ? ORDER BY s.ten_san");
    $stmt->bind_param("i", $kvId);
} elseif (isset($_GET['co_so_id'])) {
    $csId = intval($_GET['co_so_id']);
    $stmt = $conn->prepare("SELECT s.san_id, s.khu_vuc_id, s.ten_san, s.trang_thai, kv.ten_kv 
                            FROM san s JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id 
                            WHERE kv.co_so_id = ? ORDER BY kv.ten_kv, s.ten_san");
    $stmt->bind_param("i", $csId);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số khu_vuc_id hoặc co_so_id'
    ]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode([
    'success' => true, 
    'data' => $data
]);
?>