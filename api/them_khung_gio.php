<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$sanId = intval($_POST['san_id']);
$gioBatDau = $_POST['gio_bat_dau'];
$gioKetThuc = $_POST['gio_ket_thuc'];
$gia = floatval($_POST['gia']);

// Kiểm tra giờ kết thúc phải lớn hơn giờ bắt đầu
if ($gioBatDau >= $gioKetThuc) {
    echo json_encode([
        'success' => false,
        'error' => 'Giờ kết thúc phải lớn hơn giờ bắt đầu'
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

// Kiểm tra trùng / chồng lấn khung giờ
$check = $conn->prepare("SELECT khung_gio_id FROM khung_gio WHERE san_id = ? AND (? < gio_ket_thuc AND ? > gio_bat_dau)");

$check->bind_param("iss", $sanId, $gioBatDau, $gioKetThuc);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Khung giờ bị chồng lấn với khung giờ đã tồn tại'
    ]);
    exit;
}

// Thêm mới nếu không bị trùng
$insert = $conn->prepare("
    INSERT INTO khung_gio (san_id, gio_bat_dau, gio_ket_thuc, gia)
    VALUES (?, ?, ?, ?)
");

$insert->bind_param("issd", $sanId, $gioBatDau, $gioKetThuc, $gia);

if ($insert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Thêm khung giờ thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi khi thêm dữ liệu'
    ]);
}
?>