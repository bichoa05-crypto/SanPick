<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $username = $conn->real_escape_string($data['username']);
    $password = $conn->real_escape_string($data['password']);
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu'
        ]);
        exit;
    }
    $sql = "SELECT user_id, ho_ten, email, mat_khau, sdt, avatar, vai_tro, ngay_tao
            FROM users 
            WHERE (email = '$username' OR sdt = '$username')";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => $conn->error
        ]);
        exit;
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['mat_khau'])) {
            echo json_encode([
                'success' => true,
                'data' => [
                'user_id' => $user['user_id'],
                'ho_ten' => $user['ho_ten'],
                'email' => $user['email'],
                'sdt' => $user['sdt'],
                'avatar' => $user['avatar'],
                'vai_tro' => $user['vai_tro'],
                'ngay_tao' => $user['ngay_tao']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Sai tên đăng nhập hoặc mật khẩu'
        ]);
    }}
    } catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>