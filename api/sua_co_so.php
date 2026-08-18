<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$id = intval($_POST['co_so_id'] ?? 0);
$ten = trim($_POST['ten_co_so'] ?? '');
$diaChi = trim($_POST['dia_chi'] ?? '');

if ($id <= 0 || empty($ten) || empty($diaChi)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu thông tin cập nhật'
    ]);
    exit;
}
//check trùng tên cơ sở
$stmt = $conn->prepare("SELECT co_so_id FROM co_so WHERE ten_co_so = ? AND co_so_id != ?");
$stmt->bind_param("si", $ten, $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên cơ sở đã tồn tại'
    ]);
    exit;
}

if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['anh_bia']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Chỉ chấp nhận file ảnh'
        ]);
        exit;
    }
    if ($_FILES['anh_bia']['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false, 
            'error' => 'File ảnh không vượt quá 5MB'
        ]);
        exit;
    }
    $ext = pathinfo($_FILES['anh_bia']['name'], PATHINFO_EXTENSION);
    $fileName = 'cs_' . time() . '.' . $ext;
    if (move_uploaded_file($_FILES['anh_bia']['tmp_name'], '../assets/img/' . $fileName)) {
        $anhBia = 'assets/img/' . $fileName;
        $stmt = $conn->prepare("UPDATE co_so SET ten_co_so = ?, dia_chi = ?, anh_bia = ? WHERE co_so_id = ?");
        $stmt->bind_param("sssi", $ten, $diaChi, $anhBia, $id);
    }
} else {
    $stmt = $conn->prepare("UPDATE co_so SET ten_co_so = ?, dia_chi = ? WHERE co_so_id = ?");
    $stmt->bind_param("ssi", $ten, $diaChi, $id);
}

$stmt->execute();
echo json_encode([
    'success' => true, 
    'message' => 'Cập nhật cơ sở thành công'
]);
?>