<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$coSoId = intval($_POST['co_so_id'] ?? 0);
$tenKv = trim($_POST['ten_kv'] ?? '');

if ($coSoId <= 0 || empty($tenKv)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu thông tin khu vực'
    ]);
    exit;
}

// Kiểm tra trùng tên
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM khu_vuc WHERE co_so_id = ? AND ten_kv = ?");
$stmt->bind_param("is", $coSoId, $tenKv);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()['cnt'] > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên khu vực đã tồn tại trong cơ sở này'
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO khu_vuc (co_so_id, ten_kv) VALUES (?, ?)");
$stmt->bind_param("is", $coSoId, $tenKv);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'message' => 'Thêm khu vực thành công', 
    'khu_vuc_id' => $conn->insert_id
]);
?>