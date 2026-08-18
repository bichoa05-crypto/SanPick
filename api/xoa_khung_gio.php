<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if (!isset($_POST['khung_gio_id'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Thiếu tham số khung_gio_id'
    ]);
    exit;
}

$kgId = intval($_POST['khung_gio_id']);

// Kiểm tra có đơn đặt đang chờ/đã xác nhận không
$check = $conn->prepare("SELECT dat_san_id FROM dat_san WHERE khung_gio_id = ? AND trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')");
$check->bind_param("i", $kgId);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Không thể xóa vì khung giờ đang có đơn đặt'
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM khung_gio WHERE khung_gio_id = ?");
$stmt->bind_param("i", $kgId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Xóa khung giờ thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Khung giờ không tồn tại'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Lỗi khi xóa khung giờ'
    ]);
}
?>
