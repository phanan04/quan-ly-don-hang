<?php
header('Content-Type: application/json');
require_once '../config.php';

try {
    $category = isset($_GET['category']) ? $_GET['category'] : 'GPU';
    $stmt = $conn->prepare("SELECT * FROM san_pham WHERE danh_muc = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch(Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 