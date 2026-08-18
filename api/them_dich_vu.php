<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['ten_dich_vu']) || !isset($_POST['loai_dich_vu']) || !isset($_POST['don_gia'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số bắt buộc'
    ]);
    exit;
}

$ten = trim($_POST['ten_dich_vu']);
$loai = $_POST['loai_dich_vu'];
$gia = floatval($_POST['don_gia']);
$donVi = isset($_POST['don_vi']) ? trim($_POST['don_vi']) : null;
$moTa = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : null;
if (empty($ten)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên dịch vụ không được để trống'
    ]);
    exit;
}
//check trùng tên dịch vụ
$stmt = $conn->prepare("SELECT dich_vu_id FROM dich_vu WHERE ten_dich_vu = ?");
$stmt->bind_param("s", $ten);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tên dịch vụ đã tồn tại'
    ]);
    exit;
}
if (!in_array($loai, ['thue_vot', 'mua_bong', 'khac'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Loại dịch vụ không hợp lệ'
    ]);
    exit;
}
if ($gia < 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Đơn giá không được âm'
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO dich_vu (ten_dich_vu, loai_dich_vu, don_gia, don_vi, mo_ta) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdss", $ten, $loai, $gia, $donVi, $moTa);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Thêm dịch vụ thành công'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi thêm dịch vụ'
    ]);
}
?>
