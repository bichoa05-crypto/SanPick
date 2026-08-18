<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Đọc dữ liệu từ body (JSON)
$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['user_id'])) {
    echo json_encode([
        "success" => false, 
        "message" => "Dữ liệu không hợp lệ hoặc thiếu ID"
    ]);
    exit;
}

$id = intval($input['user_id']);
$ho_ten = $input['ho_ten'];
$email = $input['email'];
$sdt = $input['sdt'];
if (!$ho_ten || !$email || !$sdt) {
    echo json_encode([
        "success" => false, 
        "message" => "Vui lòng cung cấp đầy đủ thông tin"
    ]);
    exit;
}
if (!preg_match('/^0\d{9}$/', $sdt)) {
    echo json_encode([
        'success' => false,
        'error' => 'Số điện thoại không hợp lệ'
    ]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'error' => 'Email không hợp lệ'
    ]);
    exit;
}
// Kiểm tra trùng SĐT 
$check = $conn->query("SELECT user_id FROM users WHERE sdt = '$sdt' AND user_id != $id");
if ($check->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "error" => "Số điện thoại này đã tồn tại!"
    ]);
    exit;
}
//kiểm tra trùng email
$checkEmail = $conn->query("SELECT user_id FROM users WHERE email = '$email' AND user_id != $id");
if ($checkEmail->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "error" => "Email này đã tồn tại!"
    ]);
    exit;
}
try {
    $sql = "UPDATE users SET ho_ten = ?, email = ?, sdt = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $ho_ten, $email, $sdt, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true
        ]);
    } else {
        echo json_encode([
            "success" => false, 
            "message" => $stmt->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false, 
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>