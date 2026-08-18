<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$kvId = intval($_POST['khu_vuc_id'] ?? 0);
if ($kvId <= 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'ID khu vực không hợp lệ'
    ]);
    exit;
}

// Kiểm tra đơn đặt sân đang xử lý
$stmt = $conn->prepare("
    SELECT COUNT(*) as cnt FROM dat_san ds
    JOIN san s ON ds.san_id = s.san_id
    WHERE s.khu_vuc_id = ? AND ds.trang_thai IN ('cho_xac_nhan','da_xac_nhan')
");
$stmt->bind_param("i", $kvId);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()['cnt'] > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Không thể xóa vì còn đơn đặt sân đang xử lý'
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM khu_vuc WHERE khu_vuc_id = ?");
$stmt->bind_param("i", $kvId);
$stmt->execute();

echo json_encode([
    'success' => $stmt->affected_rows > 0,
    'message' => $stmt->affected_rows > 0 ? 'Xóa khu vực thành công' : 'Không tìm thấy khu vực'
]);
?>