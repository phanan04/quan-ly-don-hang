<?php
session_start();
header('Content-Type: application/json');

require_once '../config.php';

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để xem giỏ hàng'
    ]);
    exit;
}

$items = [];
$subtotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $cart_item) {
        // Get product details from database
        $stmt = $conn->prepare("SELECT id, ten_san_pham, gia_tien, hinh_anh FROM san_pham WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($product = $result->fetch_assoc()) {
            $product['quantity'] = $cart_item['quantity'];
            $items[] = $product;
            $subtotal += $product['gia_tien'] * $cart_item['quantity'];
        }
    }
}

echo json_encode([
    'status' => 'success',
    'items' => $items,
    'subtotal' => $subtotal,
    'total' => $subtotal // Add shipping cost and discounts here if needed
]); 