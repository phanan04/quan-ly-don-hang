<?php
session_start();
if(!isset($_SESSION['user'])) {
    header("Location:login.php");
    exit();
}
include 'config.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];

    // Sử dụng prepared statement để tránh SQL injection
    $stmt = $conn->prepare("DELETE FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        // Xóa thành công, chuyển hướng về trang danh sách sản phẩm
        header("Location: products.php?msg=delete_success");
        exit();
    } else {
        // Lỗi khi xóa
        echo "Lỗi khi xóa sản phẩm: " . $conn->error;
    }

    $stmt->close();
} else {
    // Không có ID sản phẩm được cung cấp
    echo "Không tìm thấy ID sản phẩm để xóa.";
}

$conn->close();

?>
