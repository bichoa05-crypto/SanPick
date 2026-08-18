<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$kvId = intval($_POST['khu_vuc_id'] ?? 0);
$tenSan = trim($_POST['ten_san'] ?? '');
$trangThai = $_POST['trang_thai'] ?? 'hoat_dong';

if ($kvId <= 0 || empty($tenSan)) {
    echo json_encode([ 
        'success' => false, 
        'error' => 'Thiếu thông tin sân'
    ]);
    exit;
}
$checkStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM san WHERE khu_vuc_id = ? AND ten_san = ?");
$checkStmt->bind_param("is", $kvId, $tenSan);
$checkStmt->execute();
if ($checkStmt->get_result()->fetch_assoc()['cnt'] > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên sân đã tồn tại trong khu vực này'
    ]);
    exit;
}
if (!in_array($trangThai, ['hoat_dong', 'bao_tri'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Trạng thái không hợp lệ'
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO san (khu_vuc_id, ten_san, trang_thai) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $kvId, $tenSan, $trangThai);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'message' => 'Thêm sân thành công', 
    'san_id' => $conn->insert_id
]);
?>