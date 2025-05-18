<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

if (!$product_id || $quantity < 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Dữ liệu không hợp lệ'
    ]);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update quantity
$_SESSION['cart'][$product_id]['quantity'] = $quantity;

echo json_encode([
    'status' => 'success',
    'message' => 'Đã cập nhật số lượng sản phẩm'
]); 