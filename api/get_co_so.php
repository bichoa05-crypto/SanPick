<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $sql = "SELECT co_so_id, ten_co_so, dia_chi,anh_bia
            FROM co_so 
            ORDER BY ten_co_so";

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