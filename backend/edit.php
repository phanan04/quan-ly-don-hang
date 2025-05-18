<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$id = $_GET['id'];
$msg = "";

// Get order details from don_hang table
$stmt_don_hang = $conn->prepare("SELECT * FROM don_hang WHERE id = ?");
$stmt_don_hang->bind_param("i", $id);
$stmt_don_hang->execute();
$result_don_hang = $stmt_don_hang->get_result();
$order = $result_don_hang->fetch_assoc();
$stmt_don_hang->close();

if (!$order) {
    die("Không tìm thấy đơn hàng");
}

// Get order items from chi_tiet_don_hang and join with san_pham
$stmt_items = $conn->prepare("SELECT
    ctdh.id,
    ctdh.san_pham_id,
    ctdh.so_luong,
    ctdh.gia_tien_don_hang,
    sp.ten_san_pham,
    sp.so_luong AS san_pham_ton_kho -- Stock quantity from san_pham
FROM chi_tiet_don_hang ctdh
JOIN san_pham sp ON ctdh.san_pham_id = sp.id
WHERE ctdh.don_hang_id = ?");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = [];
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}
$stmt_items->close();

// Get list of all products (for adding new items to order - will be used later)
$result_all_products = $conn->query("SELECT id, ten_san_pham, so_luong FROM san_pham WHERE trang_thai = 'Còn hàng'"); // Maybe filter by status 'Còn hàng'
$all_products = [];
while ($product = $result_all_products->fetch_assoc()) {
    $all_products[] = $product;
}

// Handle POST request for updating order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start transaction
    $conn->begin_transaction();

    try {
        $order_id = $_POST['order_id'] ?? null;
        $updated_status = $_POST['trang_thai'] ?? null;
        $updated_notes = $_POST['notes'] ?? null;
        $updated_export_invoice = isset($_POST['export_invoice']) ? 1 : 0; // Checkbox value
        // $updated_delivery_time = $_POST['delivery_time'] ?? null; // Removed as per request

        if (!$order_id || !$updated_status) {
             throw new Exception('Dữ liệu gửi lên không hợp lệ.');
        }

        // Get current order items from database for comparison
        $current_items_in_db = [];
        $stmt_current_items = $conn->prepare("SELECT id, san_pham_id, so_luong FROM chi_tiet_don_hang WHERE don_hang_id = ?");
        $stmt_current_items->bind_param("i", $order_id);
        $stmt_current_items->execute();
        $result_current_items = $stmt_current_items->get_result();
        while ($row = $result_current_items->fetch_assoc()) {
            $current_items_in_db[$row['id']] = $row; // Store by chi_tiet_don_hang.id
        }
        $stmt_current_items->close();

        $submitted_items = $_POST['items'] ?? []; // Existing items from form
        $new_items = $_POST['new_items'] ?? []; // New items from form

        // Process existing items (update quantity or mark for deletion)
        $items_to_update = [];
        $items_to_delete_id = array_keys($current_items_in_db); // Start with all current items as potentially deleted

        foreach ($submitted_items as $item_id => $item_data) {
            $item_id = (int) $item_id; // chi_tiet_don_hang id
            $product_id = (int) $item_data['product_id'];
            $quantity = (int) $item_data['quantity'];
            // $price_at_order = (float) $item_data['price_at_order']; // Keep original price from DB

            // If this item ID was submitted, it's not deleted, so remove from deletion list
            if (isset($current_items_in_db[$item_id])) {
                $delete_key = array_search($item_id, $items_to_delete_id);
                if ($delete_key !== false) {
                    unset($items_to_delete_id[$delete_key]);
                }

                $old_quantity = (int) $current_items_in_db[$item_id]['so_luong'];

                // Check for quantity change
                if ($quantity !== $old_quantity) {
                     // Validate stock for quantity increase
                    if ($quantity > $old_quantity) {
                         $quantity_increase = $quantity - $old_quantity;
                         $stmt_stock_check = $conn->prepare("SELECT so_luong FROM san_pham WHERE id = ?");
                         $stmt_stock_check->bind_param("i", $product_id);
                         $stmt_stock_check->execute();
                         $product_stock = $stmt_stock_check->get_result()->fetch_assoc();
                         $stmt_stock_check->close();

                         if (!$product_stock || $product_stock['so_luong'] < $quantity_increase) {
                             throw new Exception('Số lượng sản phẩm "' . htmlspecialchars($current_items_in_db[$item_id]['ten_san_pham'] ?? $product_id) . '" không đủ trong kho.');
                         }
                    }

                    $items_to_update[] = ['item_id' => $item_id, 'quantity' => $quantity, 'product_id' => $product_id, 'old_quantity' => $old_quantity];
                }
            } else {
                // This case should ideally not happen if form is correct, indicates a new item pretending to be old
                 // Handle as error or ignore
            }
        }

        // Process items to delete
        foreach ($items_to_delete_id as $item_id_to_delete) {
            $item_to_delete_data = $current_items_in_db[$item_id_to_delete];
            $product_id = $item_to_delete_data['san_pham_id'];
            $quantity = $item_to_delete_data['so_luong'];

            // Return stock for deleted item
            $stmt_return_stock = $conn->prepare("UPDATE san_pham SET so_luong = so_luong + ? WHERE id = ?");
            $stmt_return_stock->bind_param("ii", $quantity, $product_id);
             if (!$stmt_return_stock->execute()) {
                throw new Exception('Lỗi khi trả lại số lượng sản phẩm sau khi xóa: ' . $stmt_return_stock->error);
            }
            $stmt_return_stock->close();

            // Delete item from chi_tiet_don_hang
            $stmt_delete_item = $conn->prepare("DELETE FROM chi_tiet_don_hang WHERE id = ?");
            $stmt_delete_item->bind_param("i", $item_id_to_delete);
             if (!$stmt_delete_item->execute()) {
                throw new Exception('Lỗi khi xóa chi tiết đơn hàng: ' . $stmt_delete_item->error);
            }
            $stmt_delete_item->close();
        }

        // Process items to update quantity
        foreach ($items_to_update as $update_data) {
            $item_id = $update_data['item_id'];
            $new_quantity = $update_data['quantity'];
            $product_id = $update_data['product_id'];
            $old_quantity = $update_data['old_quantity'];

            // Update quantity in chi_tiet_don_hang
            $stmt_update_quantity = $conn->prepare("UPDATE chi_tiet_don_hang SET so_luong = ? WHERE id = ?");
            $stmt_update_quantity->bind_param("ii", $new_quantity, $item_id);
             if (!$stmt_update_quantity->execute()) {
                throw new Exception('Lỗi khi cập nhật số lượng chi tiết đơn hàng: ' . $stmt_update_quantity->error);
            }
            $stmt_update_quantity->close();

            // Adjust stock in san_pham
            $stock_change = $old_quantity - $new_quantity; // Positive if quantity decreased, negative if increased
            $stmt_adjust_stock = $conn->prepare("UPDATE san_pham SET so_luong = so_luong + ? WHERE id = ?");
            $stmt_adjust_stock->bind_param("ii", $stock_change, $product_id);
             if (!$stmt_adjust_stock->execute()) {
                throw new Exception('Lỗi khi điều chỉnh số lượng tồn kho: ' . $stmt_adjust_stock->error);
            }
            $stmt_adjust_stock->close();
        }

        // Process new items
        foreach ($new_items as $new_item_data) {
            $product_id = (int) $new_item_data['product_id'];
            $quantity = (int) $new_item_data['quantity'];
            // Price at order for new items needs to be fetched from san_pham

            if ($product_id <= 0 || $quantity <= 0) continue; // Skip invalid new items

            // Get product details (especially price and stock) for new item
            $stmt_new_product = $conn->prepare("SELECT gia_tien, so_luong, ten_san_pham FROM san_pham WHERE id = ?");
            $stmt_new_product->bind_param("i", $product_id);
            $stmt_new_product->execute();
            $new_product_data = $stmt_new_product->get_result()->fetch_assoc();
            $stmt_new_product->close();

            if (!$new_product_data) {
                 throw new Exception('Sản phẩm mới (ID: ' . $product_id . ') không tồn tại.');
            }
             if ($new_product_data['so_luong'] < $quantity) {
                 throw new Exception('Số lượng sản phẩm "' . htmlspecialchars($new_product_data['ten_san_pham']) . '" không đủ trong kho để thêm.');
             }
            $price_at_order = $new_product_data['gia_tien'];

            // Insert new item into chi_tiet_don_hang
            $stmt_insert_item = $conn->prepare("INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia_tien_don_hang) VALUES (?, ?, ?, ?)");
            $stmt_insert_item->bind_param("iiid", $order_id, $product_id, $quantity, $price_at_order);
             if (!$stmt_insert_item->execute()) {
                throw new Exception('Lỗi khi thêm sản phẩm mới vào đơn hàng: ' . $stmt_insert_item->error);
            }
            $stmt_insert_item->close();

            // Decrease stock for new item
            $stmt_decrease_stock = $conn->prepare("UPDATE san_pham SET so_luong = so_luong - ? WHERE id = ?");
            $stmt_decrease_stock->bind_param("ii", $quantity, $product_id);
             if (!$stmt_decrease_stock->execute()) {
                throw new Exception('Lỗi khi cập nhật số lượng tồn kho cho sản phẩm mới: ' . $stmt_decrease_stock->error);
            }
            $stmt_decrease_stock->close();
        }

        // Recalculate total amount for the order
        $recalculated_total = 0;
        $stmt_recalculate = $conn->prepare("SELECT SUM(so_luong * gia_tien_don_hang) AS total FROM chi_tiet_don_hang WHERE don_hang_id = ?");
        $stmt_recalculate->bind_param("i", $order_id);
        $stmt_recalculate->execute();
        $recalculate_result = $stmt_recalculate->get_result()->fetch_assoc();
        $stmt_recalculate->close();

        $recalculated_total = $recalculate_result['total'] ?? 0;

        // Update general order info in don_hang table
        // Note: Assuming thu_ho is same as tong_phi, adjust if needed
        $stmt_update_order = $conn->prepare("UPDATE don_hang SET tong_phi = ?, thu_ho = ?, trang_thai = ?, notes = ?, export_invoice = ? WHERE id = ?");
        $stmt_update_order->bind_param("ddssii", $recalculated_total, $recalculated_total, $updated_status, $updated_notes, $updated_export_invoice, $order_id);
         if (!$stmt_update_order->execute()) {
             throw new Exception('Lỗi khi cập nhật thông tin đơn hàng chung: ' . $stmt_update_order->error);
         }
        $stmt_update_order->close();

        // Commit transaction
        $conn->commit();

        $msg = "✔️ Cập nhật đơn hàng thành công!";
        // Reload order data after update to display fresh info on the page
        // Need to re-fetch $order and $order_items here
        // For simplicity for now, let's just show success message. Reloading data will be complex.

        // Redirect to index.php after successful update
        header("Location: index.php?msg=update_success");
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $msg = "❌ Lỗi: " . $e->getMessage();
    }
}

// Re-fetch order data to display the latest info after a potential POST update
// (This is needed because the POST handling block doesn't fully re-populate $order and $order_items)
$stmt_don_hang = $conn->prepare("SELECT * FROM don_hang WHERE id = ?");
$stmt_don_hang->bind_param("i", $id);
$stmt_don_hang->execute();
$result_don_hang = $stmt_don_hang->get_result();
$order = $result_don_hang->fetch_assoc();
$stmt_don_hang->close();

$stmt_items = $conn->prepare("SELECT
    ctdh.id,
    ctdh.san_pham_id,
    ctdh.so_luong,
    ctdh.gia_tien_don_hang,
    sp.ten_san_pham,
    sp.so_luong AS san_pham_ton_kho
FROM chi_tiet_don_hang ctdh
JOIN san_pham sp ON ctdh.san_pham_id = sp.id
WHERE ctdh.don_hang_id = ?");
$stmt_items->bind_param("i", $id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
$order_items = [];
while ($item = $result_items->fetch_assoc()) {
    $order_items[] = $item;
}
$stmt_items->close();

// Re-fetch all products as well if needed for the add new item select
$result_all_products = $conn->query("SELECT id, ten_san_pham, so_luong FROM san_pham WHERE trang_thai = 'Còn hàng'");
$all_products = [];
while ($product = $result_all_products->fetch_assoc()) {
    $all_products[] = $product;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa đơn hàng</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">GHN</div>
    <div class="user-info">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="index.php"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="main">
    <div class="form-container">
        <h3><i class="fas fa-edit"></i> Chỉnh sửa đơn hàng</h3>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <div class="form-group">
                <label>Mã đơn hàng</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($order['ma_don_hang']) ?>" disabled>
            </div>
            
            <h4>Sản phẩm trong đơn hàng</h4>
            <div id="order-items-list">
                <?php if (!empty($order_items)): ?>
                    <?php foreach ($order_items as $item): ?>
                        <div class="form-row mb-2 border rounded p-2">
                            <div class="col">
                                <label>Sản phẩm: <?= htmlspecialchars($item['ten_san_pham']) ?></label>
                                <input type="hidden" name="items[<?= $item['id'] ?>][item_id]" value="<?= $item['id'] ?>">
                                <input type="hidden" name="items[<?= $item['id'] ?>][product_id]" value="<?= $item['san_pham_id'] ?>">
                                <input type="hidden" name="items[<?= $item['id'] ?>][price_at_order]" value="<?= $item['gia_tien_don_hang'] ?>">
                            </div>
                            <div class="col">
                                <label>Số lượng:</label>
                                <input type="number" name="items[<?= $item['id'] ?>][quantity]" value="<?= $item['so_luong'] ?>" class="form-control form-control-sm" min="1" required>
                            </div>
                            <div class="col-auto d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-item">Xóa</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Đơn hàng không có sản phẩm nào.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control">
                    <option value="Chờ bàn giao" <?= ($order['trang_thai'] == 'Chờ bàn giao') ? 'selected' : '' ?>>Chờ bàn giao</option>
                    <option value="Đã bàn giao" <?= ($order['trang_thai'] == 'Đã bàn giao') ? 'selected' : '' ?>>Đã bàn giao</option>
                    <option value="Đang giao" <?= ($order['trang_thai'] == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                    <option value="Hoàn" <?= ($order['trang_thai'] == 'Hoàn') ? 'selected' : '' ?>>Hoàn Thành</option>
                </select>
            </div>

            <!-- Add other general order fields here if needed (notes, export_invoice, delivery_time) -->
            <div class="form-group">
                <label>Ghi chú đơn hàng</label>
                <textarea name="notes" class="form-control"><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
            </div>
             <div class="form-check">
                <input type="checkbox" name="export_invoice" value="1" class="form-check-input" id="editExportInvoice" <?= ($order['export_invoice'] ?? 0) ? 'checked' : '' ?>>
                <label class="form-check-label" for="editExportInvoice">Xuất hoá đơn</label>
            </div>


            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật đơn hàng</button>
            <a href="index.php" class="btn btn-default"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </form>
    </div>
</div>

<script>
    // Basic JavaScript to handle removing items (frontend visual only for now)
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.closest('.form-row').remove();
            // Need to add logic here to mark item for deletion in backend
        }
    });

    // Basic JavaScript for adding new products (frontend visual only for now)
    document.getElementById('add-product-button').addEventListener('click', function() {
        const select = document.getElementById('add-product-select');
        const selectedOption = select.options[select.selectedIndex];
        const productId = selectedOption.value;
        const productName = selectedOption.text;
        const productStock = selectedOption.dataset.stock; // Stock quantity from the option's data attribute
        // We need the actual price of the product to add it to the order item.
        // Currently, the option does not have price data.
        // For now, we'll leave price_at_order as a placeholder and rely on backend to fetch it.
        // A better approach would be to fetch the price via AJAX or store it in the option's data attribute.

        if (productId) {
            // Prevent adding the same product multiple times? (Optional, but good UX)
            // You would need to check if an input with this product_id already exists.

            // Create new item HTML
            const newItemHtml = `
                <div class="form-row mb-2 border rounded p-2" data-product-id="${productId}">
                    <div class="col">
                        <label>Sản phẩm: ${productName}</label>
                        <input type="hidden" name="new_items[][product_id]" value="${productId}">
                        <!-- Price at order for new items will be fetched by backend -->
                         <input type="hidden" name="new_items[][price_at_order]" value="0"> 
                    </div>
                    <div class="col">
                        <label>Số lượng:</label>
                        <input type="number" name="new_items[][quantity]" value="1" class="form-control form-control-sm" min="1" required>
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item">Xóa</button>
                    </div>
                </div>
            `;
            document.getElementById('order-items-list').insertAdjacentHTML('beforeend', newItemHtml);

            // Reset select
            select.value = '';
        }
    });

    // TODO: Add logic to handle deleted items on form submission
    // We need to add hidden inputs for items marked for deletion.

    // Example: Add a hidden input when a remove-item button is clicked
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            const itemRow = event.target.closest('.form-row');
            // How to differentiate between existing item and new item being removed?
            // Existing items have name like items[item_id][...]
            // New items have name like new_items[][...]

            const existingItemIdInput = itemRow.querySelector('input[name^="items["]');

            if (existingItemIdInput) {
                // This is an existing item, mark for deletion instead of just removing visually
                const itemId = existingItemIdInput.value;
                const deletedItemInput = document.createElement('input');
                deletedItemInput.type = 'hidden';
                deletedItemInput.name = 'deleted_items[]';
                deletedItemInput.value = itemId;
                itemRow.appendChild(deletedItemInput);
                itemRow.style.display = 'none'; // Hide the row visually
            } else {
                // This is a newly added item (not yet in DB), just remove visually
                itemRow.remove();
            }

            // Prevent default button click behavior
             event.preventDefault();
        }
    });

    // TODO: Add validation for stock when changing quantity or adding new items (frontend validation)
    // The backend already validates stock, but frontend validation improves UX.

    // TODO: Potentially update total amount on the frontend dynamically.

</script>

</body>
</html>