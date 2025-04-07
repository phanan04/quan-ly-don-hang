<?php
include 'config.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma = $_POST['ma_san_pham'];
    $ten = $_POST['ten_san_pham'];
    $so_luong = $_POST['so_luong'];
    $gia = $_POST['gia_tien'];
    $danh_muc = $_POST['danh_muc'];
    $trang_thai = $_POST['trang_thai'];

    // Xử lý upload ảnh
    $hinh_anh = "";
    if ($_FILES['hinh_anh']['name'] != "") {
        $hinh_anh = time() . '_' . basename($_FILES["hinh_anh"]["name"]);
        move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], "uploads/" . $hinh_anh);
    }

    // Thêm vào CSDL
    $sql = "INSERT INTO san_pham (ma_san_pham, ten_san_pham, hinh_anh, so_luong, gia_tien, danh_muc, trang_thai)
            VALUES ('$ma', '$ten', '$hinh_anh', $so_luong, $gia, '$danh_muc', '$trang_thai')";

    if ($conn->query($sql) === TRUE) {
        $msg = "✔️ Thêm sản phẩm thành công!";
    } else {
        $msg = "❌ Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thêm sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body style="padding:30px">

<div class="container">
    <h3>➕ Thêm sản phẩm mới</h3>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Mã sản phẩm</label>
            <input type="text" name="ma_san_pham" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tên sản phẩm</label>
            <input type="text" name="ten_san_pham" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Hình ảnh</label>
            <input type="file" name="hinh_anh" class="form-control">
        </div>
        <div class="form-group">
            <label>Số lượng</label>
            <input type="number" name="so_luong" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Giá tiền</label>
            <input type="number" name="gia_tien" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Danh mục</label>
            <input type="text" name="danh_muc" class="form-control">
        </div>
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trang_thai" class="form-control">
                <option value="Còn hàng">Còn hàng</option>
                <option value="Hết hàng">Hết hàng</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">💾 Lưu</button>
        <a href="index.php" class="btn btn-default">🔙 Quay lại</a>
    </form>
</div>

</body>
</html>
