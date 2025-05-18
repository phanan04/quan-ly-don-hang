<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để xóa giỏ hàng'
    ]);
    exit;
}

// Clear cart
$_SESSION['cart'] = [];

echo json_encode([
    'status' => 'success',
    'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng'
]); 