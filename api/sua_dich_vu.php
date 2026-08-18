<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['dich_vu_id']) || !isset($_POST['ten_dich_vu']) || !isset($_POST['loai_dich_vu']) || !isset($_POST['don_gia'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số bắt buộc'
    ]);
    exit;
}

$id = intval($_POST['dich_vu_id']);
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
$stmt = $conn->prepare("SELECT dich_vu_id FROM dich_vu WHERE ten_dich_vu = ? AND dich_vu_id != ?");
$stmt->bind_param("si", $ten, $id);
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

$stmt = $conn->prepare("UPDATE dich_vu SET ten_dich_vu = ?, loai_dich_vu = ?, don_gia = ?, don_vi = ?, mo_ta = ? WHERE dich_vu_id = ?");
$stmt->bind_param("ssdssi", $ten, $loai, $gia, $donVi, $moTa, $id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Cập nhật dịch vụ thành công'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi cập nhật dịch vụ'
    ]);
}
?>
