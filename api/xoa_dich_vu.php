<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['dich_vu_id'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số dich_vu_id'
    ]);
    exit;
}

$id = intval($_POST['dich_vu_id']);

$stmt = $conn->prepare("DELETE FROM dich_vu WHERE dich_vu_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Xóa dịch vụ thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Dịch vụ không tồn tại'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi xóa dịch vụ'
    ]);
}
?>
