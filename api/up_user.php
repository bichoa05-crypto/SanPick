<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $userId = $_POST['user_id'] ?? null;
    $fullname = $_POST['ho_ten'] ?? null;
    $email = $_POST['email'] ?? null;
    $sdt = $_POST['sdt'] ?? null;
    $avatar = $_POST['avatar'] ?? null; // giữ avatar cũ nếu không upload mới

    if (!$userId) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu user_id'
        ]);
        exit;
    }
    if (!$fullname || !$email || !$sdt) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu thông tin bắt buộc'
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
    // Xử lý upload avatar nếu có file
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar_file'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'error' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)'
            ]);
            exit;
        }

        if ($file['size'] > $maxSize) {
            echo json_encode([
                'success' => false,
                'error' => 'File ảnh không được vượt quá 5MB'
            ]);
            exit;
        }

        // Tạo tên file duy nhất để tránh trùng
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $uploadDir = __DIR__ . '/../assets/img/';
        $uploadPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $avatar = 'assets/img/' . $newFileName;
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Không thể lưu file ảnh'
            ]);
            exit;
        }
    }
    //kiểm tra trùng SĐT với user khác
    $checkSql = "SELECT user_id FROM users WHERE sdt = ? AND user_id != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $sdt, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Số điện thoại này đã tồn tại!'
        ]);
        exit;
    }
     //kiểm tra trùng email với user khác
     $checkESql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
     $checkEStmt = $conn->prepare($checkESql);
     $checkEStmt->bind_param("si", $email, $userId);
     $checkEStmt->execute();
     $checkEResult = $checkEStmt->get_result();
     if ($checkEResult->num_rows > 0) {
         echo json_encode([
             'success' => false,
             'error' => 'Email này đã tồn tại!'
         ]);
         exit;
     }
    $sql = "UPDATE users 
            SET ho_ten = ?, email = ?, sdt = ?, avatar = ?
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fullname, $email, $sdt, $avatar, $userId);

    if ($stmt->execute()) {
        // Lấy lại thông tin user sau khi cập nhật
        $selectSql = "SELECT user_id, ho_ten, email, sdt, avatar, vai_tro FROM users WHERE user_id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("i", $userId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $updatedUser = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $updatedUser
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