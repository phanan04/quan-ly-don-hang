<?php
session_start();
header('Content-Type: application/json');

require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Vui lòng đăng nhập để đặt hàng'
    ]);
    exit;
}

// Receive data from frontend
$data = json_decode(file_get_contents('php://input'), true);

// Basic validation (more validation will be needed)
if (!isset($data['items']) || empty($data['items'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Giỏ hàng trống hoặc dữ liệu không hợp lệ'
    ]);
    exit;
}

$user_id = $_SESSION['user']['id']; // Assuming user id is stored in session
$cart_items = $data['items'];
$notes = $data['notes'] ?? '';
$export_invoice = $data['invoice'] ?? false;
$delivery_time = $data['delivery'] ?? 'Giao ngay';

$total_amount = 0;
$order_items = [];

// Validate items and calculate total
foreach ($cart_items as $item) {
    $product_id = $item['id'] ?? null;
    $quantity = $item['quantity'] ?? 0;

    if (!$product_id || $quantity <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dữ liệu sản phẩm trong giỏ hàng không hợp lệ'
        ]);
        exit;
    }

    // Get product details from database to verify price and stock
    // Use the price from the database at the time of order, not potentially outdated price from frontend cart data
    $stmt = $conn->prepare("SELECT id, ten_san_pham, gia_tien, so_luong FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sản phẩm không tồn tại: ID ' . $product_id
        ]);
        exit;
    }

    if ($product['so_luong'] < $quantity) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Số lượng sản phẩm \"' . htmlspecialchars($product['ten_san_pham']) . '\" không đủ trong kho. Chỉ còn ' . $product['so_luong'] . ' sản phẩm.'
        ]);
        exit;
    }

    $item_price_at_order = $product['gia_tien']; // Use price from DB
    $subtotal_item = $item_price_at_order * $quantity;
    $total_amount += $subtotal_item;

    $order_items[] = [
        'product_id' => $product_id,
        'quantity' => $quantity,
        'price_at_order' => $item_price_at_order // Store price at order time
    ];
}

// Generate unique order code (example: DH + timestamp)
$ma_don_hang = "DH" . time();

$trang_thai = "Chờ xử lý"; // Initial status
$ngay_tao = date("Y-m-d H:i:s");

// Start transaction for atomicity
$conn->begin_transaction();

try {
    // Insert into don_hang table (Order header)
    // Assuming don_hang table has user_id, ma_don_hang, tong_phi, thu_ho, trang_thai, ngay_tao, notes, export_invoice, delivery_time
    // Adjust the INSERT statement and columns based on your actual don_hang table structure
    // For this example, I'll use columns that seem likely based on context, but verify against your DB schema.
    $stmt_don_hang = $conn->prepare("INSERT INTO don_hang (user_id, ma_don_hang, tong_phi, thu_ho, trang_thai, ngay_tao, notes, export_invoice, delivery_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Assuming thu_ho is same as tong_phi for now
    $thu_ho = $total_amount;
    $export_invoice_int = $export_invoice ? 1 : 0;

    $stmt_don_hang->bind_param("isdssssis", $user_id, $ma_don_hang, $total_amount, $thu_ho, $trang_thai, $ngay_tao, $notes, $export_invoice_int, $delivery_time);
    
    if (!$stmt_don_hang->execute()) {
        throw new Exception('Lỗi khi tạo đơn hàng chính: ' . $stmt_don_hang->error);
    }
    $order_id = $conn->insert_id; // Get the ID of the newly inserted order
    $stmt_don_hang->close();

    // Insert into chi_tiet_don_hang table (Order items)
    $stmt_chi_tiet = $conn->prepare("INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia_tien_don_hang) VALUES (?, ?, ?, ?)");
    
    foreach ($order_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price_at_order = $item['price_at_order'];

        $stmt_chi_tiet->bind_param("iiid", $order_id, $product_id, $quantity, $price_at_order);
        
        if (!$stmt_chi_tiet->execute()) {
            throw new Exception('Lỗi khi thêm chi tiết đơn hàng: ' . $stmt_chi_tiet->error);
        }

        // Update product quantities in san_pham table
        $update_stmt = $conn->prepare("UPDATE san_pham SET so_luong = so_luong - ? WHERE id = ?");
        $update_stmt->bind_param("ii", $quantity, $product_id);
        if (!$update_stmt->execute()) {
             throw new Exception('Lỗi khi cập nhật số lượng sản phẩm: ' . $update_stmt->error);
        }
        $update_stmt->close();
    }
    $stmt_chi_tiet->close();

    // Commit transaction if all inserts and updates were successful
    $conn->commit();

    // Clear the cart after successful order
    unset($_SESSION['cart']);

    echo json_encode([
        'status' => 'success',
        'message' => 'Đặt hàng thành công!',
        'order_code' => $ma_don_hang,
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}

?> 