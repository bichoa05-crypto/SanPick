<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $sql = "SELECT dich_vu_id, ten_dich_vu, loai_dich_vu, don_gia, don_vi, mo_ta
            FROM dich_vu 
            ORDER BY ten_dich_vu";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => $conn->error
        ]);
        exit;
    }

    $data = [];

    while ($row = $result->fetch_assoc()) {
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
?>