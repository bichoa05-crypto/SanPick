<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $co_so_id = isset($_GET['co_so_id']) ? intval($_GET['co_so_id']) : 0;
    $trang_thai = $_GET['trang_thai'] ?? null;
    $ngay = $_GET['ngay'] ?? null;

    if (!$co_so_id) {
        echo json_encode([
            'success' => false, 
            'error' => 'Thiếu co_so_id'
        ]);
        exit;
    }

    $sql = "SELECT ds.dat_san_id, ds.ngay_dat, ds.tien_san, ds.tien_dich_vu, ds.tong_hoa_don, 
                   ds.trang_thai, ds.ngay_tao,
                   s.ten_san, s.san_id,
                   kv.ten_kv,
                   cs.ten_co_so,
                   kg.gio_bat_dau, kg.gio_ket_thuc,
                   u.ho_ten, u.sdt, u.email
            FROM dat_san ds
            JOIN san s ON ds.san_id = s.san_id
            JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id
            JOIN co_so cs ON kv.co_so_id = cs.co_so_id
            JOIN khung_gio kg ON ds.khung_gio_id = kg.khung_gio_id
            JOIN users u ON ds.khach_hang_id = u.user_id
            WHERE cs.co_so_id = ?";

    $params = [$co_so_id];
    $types = "i";

    if ($trang_thai) {
        $allowed = ['cho_xac_nhan', 'da_xac_nhan', 'hoan_thanh', 'da_huy'];
        if (in_array($trang_thai, $allowed)) {
            $sql .= " AND ds.trang_thai = ?";
            $params[] = $trang_thai;
            $types .= "s";
        }
    }

    if ($ngay) {
        $sql .= " AND ds.ngay_dat = ?";
        $params[] = $ngay;
        $types .= "s";
    }

    $sql .= " ORDER BY ds.ngay_tao DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
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
        $data[] = $row;
    }

    echo json_encode([  
        'success' => true, 
        'data' => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
