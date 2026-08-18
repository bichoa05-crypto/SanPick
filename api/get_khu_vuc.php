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

    $sql = "SELECT khu_vuc_id, ten_kv
            FROM khu_vuc 
            WHERE co_so_id = $co_so_id 
            ORDER BY ten_kv";

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