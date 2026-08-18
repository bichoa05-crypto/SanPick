<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$sanId = intval($_POST['san_id'] ?? 0);
if ($sanId <= 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'ID sân không hợp lệ'
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM san WHERE san_id = ?");
$stmt->bind_param("i", $sanId);
$stmt->execute();

echo json_encode([
    'success' => $stmt->affected_rows > 0,
    'message' => $stmt->affected_rows > 0 ? 'Xóa sân thành công' : 'Không tìm thấy sân'
]);
?>