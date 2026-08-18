<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['khung_gio_id']) || !isset($_POST['gio_bat_dau']) || !isset($_POST['gio_ket_thuc']) || !isset($_POST['gia'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số bắt buộc'
    ]);
    exit;
}

$kgId = intval($_POST['khung_gio_id']);
$gioBatDau = $conn->real_escape_string($_POST['gio_bat_dau']);
$gioKetThuc = $conn->real_escape_string($_POST['gio_ket_thuc']);
$gia = floatval($_POST['gia']);

if ($gioBatDau >= $gioKetThuc) {
    echo json_encode([
        'success' => false,
         'error' => 'Giờ bắt đầu phải nhỏ hơn giờ kết thúc'
         ]);
    exit;
}
if ($gia < 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Giá không được âm'
    ]);
    exit;
}

// Kiểm tra trùng khung giờ (trừ chính nó)
$check = $conn->prepare("SELECT san_id FROM khung_gio WHERE khung_gio_id = ?");
$check->bind_param("i", $kgId);
$check->execute();
$row = $check->get_result()->fetch_assoc();
if (!$row) {
    echo json_encode([
        'success' => false,
        'error' => 'Khung giờ không tồn tại'
    ]);
    exit;
}
$sanId = $row['san_id'];
// Kiểm tra trùng / chồng lấn khung giờ khi cập nhật
$checkgio = $conn->prepare("SELECT khung_gio_id FROM khung_gio
                                 WHERE san_id = ? AND khung_gio_id != ? AND (? < gio_ket_thuc AND ? > gio_bat_dau)");
$checkgio->bind_param("iiss", $sanId, $kgId, $gioBatDau, $gioKetThuc);
$checkgio->execute();
$resultOverlap = $checkgio->get_result();
if ($resultOverlap->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Khung giờ này chồng lấn với khung giờ khác'
    ]);
    exit;
}


$stmt = $conn->prepare("UPDATE khung_gio SET gio_bat_dau = ?, gio_ket_thuc = ?, gia = ? WHERE khung_gio_id = ?");
$stmt->bind_param("ssdi", $gioBatDau, $gioKetThuc, $gia, $kgId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật khung giờ thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi cập nhật khung giờ'
    ]);
}
?>
