<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'] ?? null;
    $newPassword = $input['new_password'] ?? null;
    if (!$userId || !$newPassword) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu user_id hoặc new_password'
        ]);
        exit;
    }
    if (strlen($newPassword) < 6) {
        echo json_encode([
            'success' => false,
            'error' => 'Mật khẩu mới phải có ít nhất 6 ký tự'
        ]);
        exit;
    }
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET mat_khau = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $userId);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật mật khẩu thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $stmt->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>