<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy thông tin ảnh để xoá file nếu có
    $result = $conn->query("SELECT hinh_anh FROM san_pham WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row && $row['hinh_anh'] != '') {
        $anh = 'uploads/' . $row['hinh_anh'];
        if (file_exists($anh)) {
            unlink($anh); // xoá file ảnh khỏi thư mục uploads
        }
    }

    // Xoá sản phẩm khỏi CSDL
    $sql = "DELETE FROM san_pham WHERE id = $id";
    $conn->query($sql);
}

// Sau khi xoá, quay lại index
header("Location: index.php");
exit();
?>
