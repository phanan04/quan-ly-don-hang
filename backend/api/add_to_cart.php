<?php
session_start();
header('Content-Type: application/json');

// Debug session information
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
        'debug' => [
            'session_id' => session_id(),
            'session_data' => $_SESSION
        ]
    ]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

if (!$product_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu thông tin sản phẩm'
    ]);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update product in cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'quantity' => $quantity
    ];
}

echo json_encode([
    'status' => 'success',
    'message' => 'Đã thêm sản phẩm vào giỏ hàng',
    'cart_count' => count($_SESSION['cart'])
]); 