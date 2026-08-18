<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    if (!isset($_GET['co_so_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Thiếu tham số co_so_id'
        ]);
        exit;
    }

    $co_so_id = intval($_GET['co_so_id']);

    $sql = "SELECT cs.co_so_id, cs.ten_co_so, cs.dia_chi, cs.anh_bia, Count(DISTINCT s.san_id) AS so_san
            FROM co_so cs
            LEFT JOIN khu_vuc kv ON cs.co_so_id = kv.co_so_id
            LEFT JOIN san s ON kv.khu_vuc_id = s.khu_vuc_id
            WHERE cs.co_so_id = $co_so_id
            GROUP BY cs.co_so_id";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => $conn->error
        ]);
        exit;
    }

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Không tìm thấy cơ sở'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}