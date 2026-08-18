<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['co_so_id']) || !isset($_FILES['hinh_anh'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số co_so_id hoặc file hình ảnh'
    ]);
    exit;
}

$coSoId = intval($_POST['co_so_id']);

// Kiểm tra cơ sở tồn tại
$check = $conn->prepare("SELECT co_so_id FROM co_so WHERE co_so_id = ?");
$check->bind_param("i", $coSoId);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Cơ sở không tồn tại'
    ]);
    exit;
}

$file = $_FILES['hinh_anh'];
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)'
    ]);
    exit;
}
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode([
        'success' => false, 
        'error' => 'File ảnh không được vượt quá 5MB'
    ]);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'cs' . $coSoId . '_' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
$uploadDir = '../assets/img/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
$uploadPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi tải ảnh lên'
    ]);
    exit;
}

$duongDan = 'assets/img/' . $filename;
$stmt = $conn->prepare("INSERT INTO hinh_anh_co_so (co_so_id, duong_dan) VALUES (?, ?)");
$stmt->bind_param("is", $coSoId, $duongDan);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Thêm ảnh thành công', 
        'data' => [
            'hinh_id' => $stmt->insert_id, 
            'duong_dan' => $duongDan
        ]
    ]);
} else {
    unlink($uploadPath);
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi lưu vào database'
    ]);
}
?>
