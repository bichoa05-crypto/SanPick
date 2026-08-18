<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $input['user_id'] ?? null;
    $sanId = $input['san_id'] ?? null;
    $khungGioId = $input['khung_gio_id'] ?? null;
    $ngayDat = $input['ngay_dat'] ?? null;
    $tienSan = $input['tien_san'] ?? null;
    $dichVu = $input['dich_vu'] ?? [];
    $tienDichVu = 0;
    foreach ($dichVu as $dv){
        $tienDichVu += $dv['thanh_tien'] ?? 0;
    }
    $tongHoaDon = $tienSan + $tienDichVu;

    if (!$userId || !$sanId || !$khungGioId || !$ngayDat || !$tienSan) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu thông tin bắt buộc'
        ]);
        exit;
    }

    // Kiểm tra xem sân đã được đặt vào khung giờ và ngày đó chưa
    $checkSql = "SELECT COUNT(*) FROM dat_san 
                 WHERE san_id = ? AND khung_gio_id = ? AND ngay_dat = ? AND trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("iis", $sanId, $khungGioId, $ngayDat);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->fetch_row()[0] > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Sân đã được đặt vào khung giờ và ngày này'
        ]);
        exit;
    }

    // Thêm đơn đặt sân mới
    $insertSql = "INSERT INTO dat_san (khach_hang_id, san_id, khung_gio_id, ngay_dat, tien_san, tien_dich_vu, tong_hoa_don, trang_thai) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'cho_xac_nhan')";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("iiisddd", $userId, $sanId, $khungGioId, $ngayDat, $tienSan, $tienDichVu, $tongHoaDon);
    if ($insertStmt->execute()) {
        $datSanId = $insertStmt->insert_id;
        // Thêm chi tiết dịch vụ nếu có
        if (!empty($dichVu)) {
            $dvInsertSql = "INSERT INTO chi_tiet_dich_vu (dat_san_id, dich_vu_id, so_luong, thanh_tien) VALUES (?, ?, ?, ?)";
            $dvInsertStmt = $conn->prepare($dvInsertSql);
            foreach ($dichVu as $dv) {
                $dvId = $dv['dich_vu_id'] ?? null;
                $soLuong = $dv['so_luong'] ?? null;
                $thanhTien = $dv['thanh_tien'] ?? null;
                if ($dvId && $soLuong && $thanhTien) {
                    $dvInsertStmt->bind_param("iiid", $datSanId, $dvId, $soLuong, $thanhTien);
                    $dvInsertStmt->execute();
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Đặt sân thành công',
            'dat_san_id' => $datSanId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Lỗi khi đặt sân: ' . $conn->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
}
?>