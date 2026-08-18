<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $co_so_id = isset($_GET['co_so_id']) ? intval($_GET['co_so_id']) : 0;
    $thang = isset($_GET['thang']) ? intval($_GET['thang']) : 0;
    $nam = isset($_GET['nam']) ? intval($_GET['nam']) : intval(date('Y'));

    if ($co_so_id <= 0) {
        echo json_encode([
            'success' => false, 
            'error' => 'Thiếu co_so_id'
        ]);
        exit;
    }

    $baseJoin = "FROM dat_san ds 
        JOIN san s ON ds.san_id = s.san_id 
        JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id 
        WHERE kv.co_so_id = ?";
    $dateFilter = "";
    $params = [$co_so_id];
    $types = "i";

    if ($thang > 0) {
        $dateFilter = " AND MONTH(ds.ngay_dat) = ? AND YEAR(ds.ngay_dat) = ?";
        $params[] = $thang;
        $params[] = $nam;
        $types .= "ii";
    } else {
        $dateFilter = " AND YEAR(ds.ngay_dat) = ?";
        $params[] = $nam;
        $types .= "i";
    }

    // Tổng quan
    $sql = "SELECT 
        COUNT(*) as tong_don,
        SUM(CASE WHEN ds.trang_thai = 'hoan_thanh' THEN ds.tong_hoa_don ELSE 0 END) as tong_doanh_thu,
        SUM(CASE WHEN ds.trang_thai = 'hoan_thanh' THEN ds.tien_san ELSE 0 END) as doanh_thu_san,
        SUM(CASE WHEN ds.trang_thai = 'hoan_thanh' THEN ds.tien_dich_vu ELSE 0 END) as doanh_thu_dich_vu,
        COUNT(DISTINCT ds.khach_hang_id) as tong_khach,
        SUM(CASE WHEN ds.trang_thai = 'cho_xac_nhan' THEN 1 ELSE 0 END) as cho_xac_nhan,
        SUM(CASE WHEN ds.trang_thai = 'da_xac_nhan' THEN 1 ELSE 0 END) as da_xac_nhan,
        SUM(CASE WHEN ds.trang_thai = 'hoan_thanh' THEN 1 ELSE 0 END) as hoan_thanh,
        SUM(CASE WHEN ds.trang_thai = 'da_huy' THEN 1 ELSE 0 END) as da_huy
        $baseJoin $dateFilter";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $tong_quan = $stmt->get_result()->fetch_assoc();

    // Top sân
    $sql3 = "SELECT s.ten_san, kv.ten_kv, COUNT(*) as so_luot,
        SUM(CASE WHEN ds.trang_thai != 'da_huy' THEN ds.tong_hoa_don ELSE 0 END) as doanh_thu
        $baseJoin $dateFilter
        GROUP BY ds.san_id ORDER BY so_luot DESC LIMIT 5";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param($types, ...$params);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    $top_san = [];
    while ($r = $res3->fetch_assoc()) $top_san[] = $r;

    // Top khách
    $sql4 = "SELECT u.ho_ten, u.sdt, COUNT(*) as so_don,
        SUM(ds.tong_hoa_don) as tong_chi
        FROM dat_san ds 
        JOIN san s ON ds.san_id = s.san_id 
        JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id 
        JOIN users u ON ds.khach_hang_id = u.user_id
        WHERE kv.co_so_id = ? AND ds.trang_thai != 'da_huy'" . $dateFilter . "
        GROUP BY ds.khach_hang_id ORDER BY tong_chi DESC LIMIT 5";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param($types, ...$params);
    $stmt4->execute();
    $res4 = $stmt4->get_result();
    $top_khach = [];
    while ($r = $res4->fetch_assoc()) $top_khach[] = $r;

    echo json_encode([
        'success' => true,
        'data' => [
            'tong_quan' => $tong_quan,
            'top_san' => $top_san,
            'top_khach' => $top_khach
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
