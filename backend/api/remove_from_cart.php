<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để xóa sản phẩm khỏi giỏ hàng'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;

if (!$product_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu thông tin sản phẩm'
    ]);
    exit;
}

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

echo json_encode([
    'status' => 'success',
    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
]); 