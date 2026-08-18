<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$sanId = intval($_POST['san_id'] ?? 0);
$tenSan = trim($_POST['ten_san'] ?? '');
$trangThai = $_POST['trang_thai'] ?? '';

if ($sanId <= 0 || empty($tenSan)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu thông tin cập nhật'
    ]);
    exit;
}
//check trùng tên sân
$stmtOld = $conn->prepare("SELECT ten_san FROM san WHERE san_id = ?");
$stmtOld->bind_param("i", $sanId);
$stmtOld->execute();
$oldResult = $stmtOld->get_result()->fetch_assoc();

if ($oldResult && $oldResult['ten_san'] !== $tenSan) {
    $stmt = $conn->prepare("SELECT san_id FROM san WHERE ten_san = ? AND san_id != ?");
    $stmt->bind_param("si", $tenSan, $sanId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false, 
            'error' => 'Tên sân đã tồn tại'
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
}

$stmt = $conn->prepare("UPDATE san SET ten_san = ?, trang_thai = ? WHERE san_id = ?");
$stmt->bind_param("ssi", $tenSan, $trangThai, $sanId);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'message' => 'Cập nhật sân thành công'
]);
?>