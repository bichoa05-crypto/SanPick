<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$id = intval($_POST['co_so_id'] ?? 0);
if ($id <= 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'ID cơ sở không hợp lệ'
    ]);
    exit;
}

// Kiểm tra đơn đặt sân đang xử lý
$stmt = $conn->prepare("
    SELECT COUNT(*) as cnt FROM dat_san ds
    JOIN san s ON ds.san_id = s.san_id
    JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id
    WHERE kv.co_so_id = ? AND ds.trang_thai IN ('cho_xac_nhan','da_xac_nhan')
");
$stmt->bind_param("i", $id);
$stmt->execute();
$check = $stmt->get_result()->fetch_assoc();

if ($check['cnt'] > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Không thể xóa vì còn đơn đặt sân đang xử lý'
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM co_so WHERE co_so_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode([
    'success' => $stmt->affected_rows > 0,
    'message' => $stmt->affected_rows > 0 ? 'Xóa cơ sở thành công' : 'Không tìm thấy cơ sở'
]);
?>