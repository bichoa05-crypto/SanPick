<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['hinh_id'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số hinh_id'
    ]);
    exit;
}

$hinhId = intval($_POST['hinh_id']);

// Lấy đường dẫn ảnh để xóa file
$stmt = $conn->prepare("SELECT duong_dan FROM hinh_anh_co_so WHERE hinh_id = ?");
$stmt->bind_param("i", $hinhId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Ảnh không tồn tại'
    ]);
    exit;
}

$row = $result->fetch_assoc();
$filePath = '../' . $row['duong_dan'];

// Xóa trong database
$del = $conn->prepare("DELETE FROM hinh_anh_co_so WHERE hinh_id = ?");
$del->bind_param("i", $hinhId);

if ($del->execute()) {
    // Xóa file vật lý nếu tồn tại
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    echo json_encode([
        'success' => true, 
        'message' => 'Xóa ảnh thành công'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi xóa ảnh'
    ]);
}
?>
