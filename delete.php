<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin đơn hàng để trả lại số lượng sản phẩm
    $result = $conn->query("SELECT san_pham_id, so_luong FROM don_hang WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row) {
        $san_pham_id = $row['san_pham_id'];
        $so_luong = $row['so_luong'];

        // Trả lại số lượng sản phẩm vào kho
        $conn->query("UPDATE san_pham SET so_luong = so_luong + $so_luong WHERE id = $san_pham_id");
    }

    // Xóa đơn hàng
    $sql = "DELETE FROM don_hang WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=delete_success");
    } else {
        header("Location: index.php?msg=delete_error");
    }
} else {
    header("Location: index.php");
}
exit();
?>