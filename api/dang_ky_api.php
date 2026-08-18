<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $ho_ten = trim($data['ho_ten'] ?? '');
    $email = trim($data['email'] ?? '');
    $sdt = trim($data['sdt'] ?? '');
    $mat_khau = $data['mat_khau'] ?? '';

    if (empty($ho_ten) || empty($email) || empty($sdt) || empty($mat_khau)) {
        echo json_encode([
            'success' => false, 
            'error' => 'Vui lòng nhập đầy đủ thông tin'
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

    if (!preg_match('/^0\d{9}$/', $sdt)) {
        echo json_encode([
            'success' => false,
            'error' => 'Số điện thoại không hợp lệ'
        ]);
        exit;
    }

    if (strlen($mat_khau) < 6) {
        echo json_encode([
            'success' => false,
            'error' => 'Mật khẩu phải có ít nhất 6 ký tự'
        ]);
        exit;
    }

    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Email đã được sử dụng'
        ]);
        exit;
    }
    $stmt->close();

    // Kiểm tra SĐT đã tồn tại
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE sdt = ?");
    $stmt->bind_param("s", $sdt);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Số điện thoại đã được sử dụng'
        ]);
        exit;
    }
    $stmt->close();

    // Hash mật khẩu và insert
    $hashed = password_hash($mat_khau, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (ho_ten, email, mat_khau, sdt, vai_tro) VALUES (?, ?, ?, ?, 'khach_hang')");
    $stmt->bind_param("ssss", $ho_ten, $email, $hashed, $sdt);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Đăng ký thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Đăng ký thất bại'
        ]);
    }
    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi hệ thống'
    ]);
}
?>
