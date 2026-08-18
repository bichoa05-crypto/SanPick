<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$ten = trim($_POST['ten_co_so'] ?? '');
$diaChi = trim($_POST['dia_chi'] ?? '');

if (empty($ten) || empty($diaChi)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên cơ sở và địa chỉ không được để trống'
    ]);
    exit;
}
//check trùng tên cơ sở
$stmt = $conn->prepare("SELECT co_so_id FROM co_so WHERE ten_co_so = ?");
$stmt->bind_param("s", $ten);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên cơ sở đã tồn tại'
    ]);
    exit;
}

$anhBia = null;
if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['anh_bia']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Chỉ chấp nhận file ảnh (jpg, png, gif, webp)'
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
    }
}

$stmt = $conn->prepare("INSERT INTO co_so (ten_co_so, dia_chi, anh_bia) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $ten, $diaChi, $anhBia);
$stmt->execute();

echo json_encode([
    'success' => true, 
    'message' => 'Thêm cơ sở thành công', 
    'co_so_id' => $conn->insert_id
]);
?>