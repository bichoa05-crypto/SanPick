<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$kvId = intval($_POST['khu_vuc_id'] ?? 0);
$tenKv = trim($_POST['ten_kv'] ?? '');

if ($kvId <= 0 || empty($tenKv)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu thông tin cập nhật'
    ]);
    exit;
}

// Kiểm tra trùng tên trong cùng cơ sở (trừ chính nó)
$stmt = $conn->prepare("
    SELECT COUNT(*) as cnt FROM khu_vuc 
    WHERE co_so_id = (SELECT co_so_id FROM khu_vuc WHERE khu_vuc_id = ?) 
    AND ten_kv = ? AND khu_vuc_id != ?
");
$stmt->bind_param("isi", $kvId, $tenKv, $kvId);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()['cnt'] > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên khu vực đã tồn tại'
    ]);
    exit;
}

$stmt = $conn->prepare("UPDATE khu_vuc SET ten_kv = ? WHERE khu_vuc_id = ?");
$stmt->bind_param("si", $tenKv, $kvId);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'message' => 'Cập nhật khu vực thành công'
]);
?>