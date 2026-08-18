<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$input = json_decode(file_get_contents("php://input"), true);
$id = isset($input['user_id']) ? intval($input['user_id']) : 0;

// 4. Kiểm tra ID hợp lệ
if ($id <= 0) {
    echo json_encode([
        "success" => false, 
        "message" => "ID không hợp lệ"
    ]);
    exit;
}

try {
    $sql = "DELETE FROM users WHERE user_id = ? AND vai_tro = 'khach_hang'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode([
            "success" => false, 
            "message" => "Lỗi chuẩn bị câu lệnh: " . $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true
            ]);
        } else {
            echo json_encode([
                "success" => false, 
                "message" => "Không tìm thấy khách hàng này để xóa"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false, 
            "message" => "Lỗi thực thi: " . $stmt->error
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Lỗi hệ thống: " . $e->getMessage()
    ]);
}

$conn->close();
?>