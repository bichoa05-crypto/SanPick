<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

$ngay = isset($_GET['ngay']) ? $conn->real_escape_string($_GET['ngay']) : date('Y-m-d');

if (isset($_GET['san_id'])) {
    $sanId = intval($_GET['san_id']);
    $stmt = $conn->prepare("SELECT kg.khung_gio_id, kg.san_id, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia, s.ten_san,
                    CASE 
                       WHEN EXISTS ( SELECT 1 FROM dat_san ds WHERE ds.khung_gio_id = kg.khung_gio_id AND ds.ngay_dat = ? AND ds.trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
                       ) THEN 'da_dat'
                       ELSE 'trong' 
                    END AS trang_thai_dat
                    FROM khung_gio kg
                    JOIN san s ON kg.san_id = s.san_id
                    WHERE kg.san_id = ?
                    ORDER BY kg.gio_bat_dau");
    $stmt->bind_param("si", $ngay, $sanId);
} elseif (isset($_GET['khu_vuc_id'])) {
    $kvId = intval($_GET['khu_vuc_id']);
    $stmt = $conn->prepare("SELECT kg.khung_gio_id, kg.san_id, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia, s.ten_san,
                    CASE 
                       WHEN EXISTS ( SELECT 1 FROM dat_san ds WHERE ds.khung_gio_id = kg.khung_gio_id AND ds.ngay_dat = ? AND ds.trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
                       ) THEN 'da_dat'
                       ELSE 'trong' 
                    END AS trang_thai_dat
                    FROM khung_gio kg
                    JOIN san s ON kg.san_id = s.san_id
                    WHERE s.khu_vuc_id = ?
                    ORDER BY s.ten_san, kg.gio_bat_dau");
    $stmt->bind_param("si", $ngay, $kvId);
} elseif (isset($_GET['co_so_id'])) {
    $csId = intval($_GET['co_so_id']);
    $stmt = $conn->prepare("SELECT kg.khung_gio_id, kg.san_id, kg.gio_bat_dau, kg.gio_ket_thuc, kg.gia, s.ten_san,
                    CASE 
                       WHEN EXISTS ( SELECT 1 FROM dat_san ds WHERE ds.khung_gio_id = kg.khung_gio_id AND ds.ngay_dat = ? AND ds.trang_thai IN ('cho_xac_nhan', 'da_xac_nhan')
                       ) THEN 'da_dat'
                       ELSE 'trong' 
                    END AS trang_thai_dat
                    FROM khung_gio kg
                    JOIN san s ON kg.san_id = s.san_id
                    JOIN khu_vuc kv ON s.khu_vuc_id = kv.khu_vuc_id
                    WHERE kv.co_so_id = ?
                    ORDER BY s.ten_san, kg.gio_bat_dau");
    $stmt->bind_param("si", $ngay, $csId);
} else {
    echo json_encode([
        'success' => false,
         'error' => 'Thiếu tham số san_id, khu_vuc_id hoặc co_so_id'
         ]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode([
    'success' => true, 
    'data' => $data
    ]);
