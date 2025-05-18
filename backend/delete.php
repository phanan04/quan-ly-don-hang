<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get order items to return stock
        $stmt_items = $conn->prepare("SELECT san_pham_id, so_luong FROM chi_tiet_don_hang WHERE don_hang_id = ?");
        $stmt_items->bind_param("i", $id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();

        while ($item = $result_items->fetch_assoc()) {
            $san_pham_id = $item['san_pham_id'];
            $so_luong = $item['so_luong'];

            // Return stock to san_pham table
            $stmt_update_stock = $conn->prepare("UPDATE san_pham SET so_luong = so_luong + ? WHERE id = ?");
            $stmt_update_stock->bind_param("ii", $so_luong, $san_pham_id);
            if (!$stmt_update_stock->execute()) {
                throw new Exception('Lỗi khi trả lại số lượng sản phẩm: ' . $stmt_update_stock->error);
            }
            $stmt_update_stock->close();
        }
        $stmt_items->close();

        // Delete the order from don_hang table
        // chi_tiet_don_hang records will be deleted automatically due to ON DELETE CASCADE
        $stmt_delete_order = $conn->prepare("DELETE FROM don_hang WHERE id = ?");
        $stmt_delete_order->bind_param("i", $id);
        
        if (!$stmt_delete_order->execute()) {
            throw new Exception('Lỗi khi xóa đơn hàng: ' . $stmt_delete_order->error);
        }
        $stmt_delete_order->close();

        // Commit transaction
        $conn->commit();

        header("Location: index.php?msg=delete_success");

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        // Optionally log the error $e->getMessage()
        header("Location: index.php?msg=delete_error&error_detail=" . urlencode($e->getMessage()));
    }

} else {
    header("Location: index.php");
}
exit();
?>