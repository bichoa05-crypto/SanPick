<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $datSanId = $input['dat_san_id'] ?? null;
    $userId = $input['user_id'] ?? null;

    if (!$datSanId || !$userId) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu thông tin'
        ]);
        exit;
    }

    // Chỉ cho phép hủy đơn thuộc về user và đang ở trạng thái cho_xac_nhan
    $sql = "UPDATE dat_san SET trang_thai = 'da_huy' 
            WHERE dat_san_id = ? AND khach_hang_id = ? AND trang_thai = 'cho_xac_nhan'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $datSanId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $dvXoa = "DELETE FROM chi_tiet_dich_vu WHERE dat_san_id = ?";
        $dvStmt = $conn->prepare($dvXoa);
        $dvStmt->bind_param("i", $datSanId);
        $dvStmt->execute();
        echo json_encode([
            'success' => true,
            'message' => 'Đã hủy đơn đặt sân thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Không thể hủy đơn này'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>