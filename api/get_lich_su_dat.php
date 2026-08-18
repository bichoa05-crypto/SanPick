<?php 
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? null;

    if (!$userId) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu user_id'
        ]);
        exit;
    }

    // Lấy danh sách đặt sân kèm thông tin sân, khu vực, cơ sở, khung giờ
    $sql = "SELECT ds.dat_san_id, ds.ngay_dat, ds.tien_san, ds.tien_dich_vu, ds.tong_hoa_don, 
                   ds.trang_thai, ds.ngay_tao,
                   s.ten_san, 
                   kv.ten_kv,
                   cs.ten_co_so,
                   kg.gio_bat_dau, kg.gio_ket_thuc
            FROM dat_san ds
            JOIN san s ON ds.san_id = s.san_id
            JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id
            JOIN co_so cs ON kv.co_so_id = cs.co_so_id
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            WHERE ds.khach_hang_id = ?
            ORDER BY ds.ngay_tao DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $lichSuDat = [];
    while ($row = $result->fetch_assoc()) {
        // Lấy dịch vụ kèm theo cho mỗi đơn đặt sân
        $dvSql = "SELECT dv.ten_dich_vu, ctdv.so_luong, ctdv.thanh_tien
                  FROM chi_tiet_dich_vu ctdv
                  JOIN dich_vu dv ON ctdv.dich_vu_id = dv.dich_vu_id
                  WHERE ctdv.dat_san_id = ?";
        $dvStmt = $conn->prepare($dvSql);
        $dvStmt->bind_param("i", $row['dat_san_id']);
        $dvStmt->execute();
        $dvResult = $dvStmt->get_result();

        $dichVu = [];
        while ($dvRow = $dvResult->fetch_assoc()) {
            $dichVu[] = $dvRow;
        }
        $row['dich_vu'] = $dichVu;

        $lichSuDat[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $lichSuDat
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>