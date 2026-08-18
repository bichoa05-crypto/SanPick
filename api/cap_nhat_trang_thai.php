<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $dat_san_id = isset($input['dat_san_id']) ? intval($input['dat_san_id']) : 0;
    $trang_thai = $input['trang_thai'] ?? null;

    if (!$dat_san_id || !$trang_thai) {
        echo json_encode([
            'success' => false, 
            'error' => 'Thiếu dat_san_id hoặc trang_thai'
        ]);
        exit;
    }

    $allowed = ['cho_xac_nhan', 'da_xac_nhan', 'hoan_thanh', 'da_huy'];
    if (!in_array($trang_thai, $allowed)) {
        echo json_encode([
            'success' => false,
            'error' => 'Trạng thái không hợp lệ'
        ]);
        exit;
    }

    // Kiểm tra trạng thái hiện tại để đảm bảo chuyển đổi hợp lệ
    $checkSql = "SELECT trang_thai FROM dat_san WHERE dat_san_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $dat_san_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Không tìm thấy đơn đặt sân'
        ]);
        exit;
    }

    $current = $checkResult->fetch_assoc()['trang_thai'];

    // Quy tắc chuyển trạng thái
    $validTransitions = [
        'cho_xac_nhan' => ['da_xac_nhan', 'da_huy'],
        'da_xac_nhan'  => ['hoan_thanh', 'da_huy'],
    ];

    if (!isset($validTransitions[$current]) || !in_array($trang_thai, $validTransitions[$current])) {
        echo json_encode([
            'success' => false,
            'error' => "Không thể chuyển từ '$current' sang '$trang_thai'"
        ]);
        exit;
    }

    $sql = "UPDATE dat_san SET trang_thai = ? WHERE dat_san_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $trang_thai, $dat_san_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Không có thay đổi'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
