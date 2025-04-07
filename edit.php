<?php
include 'config.php';

$id = $_GET['id'];
$msg = "";

// Lấy dữ liệu sản phẩm cũ
$sql = "SELECT * FROM san_pham WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    die("Không tìm thấy sản phẩm");
}

// Khi bấm cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ma = $_POST['ma_san_pham'];
    $ten = $_POST['ten_san_pham'];
    $so_luong = $_POST['so_luong'];
    $gia = $_POST['gia_tien'];
    $danh_muc = $_POST['danh_muc'];
    $trang_thai = $_POST['trang_thai'];

    // Cập nhật ảnh nếu có ảnh mới
    $hinh_anh = $row['hinh_anh'];
    if ($_FILES['hinh_anh']['name'] != "") {
        $hinh_anh = time() . '_' . basename($_FILES["hinh_anh"]["name"]);
        move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], "uploads/" . $hinh_anh);
    }

    $sql_update = "UPDATE san_pham SET
        ma_san_pham='$ma',
        ten_san_pham='$ten',
        hinh_anh='$hinh_anh',
        so_luong=$so_luong,
        gia_tien=$gia,
        danh_muc='$danh_muc',
        trang_thai='$trang_thai'
        WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        $msg = "✔️ Cập nhật thành công!";
        // Cập nhật lại dữ liệu sau khi sửa
        $row = $conn->query("SELECT * FROM san_pham WHERE id = $id")->fetch_assoc();
    } else {
        $msg = "❌ Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body style="padding:30px">

<div class="container">
    <h3>✏️ Sửa sản phẩm</h3>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Mã sản phẩm</label>
            <input type="text" name="ma_san_pham" value="<?= $row['ma_san_pham'] ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tên sản phẩm</label>
            <input type="text" name="ten_san_pham" value="<?= $row['ten_san_pham'] ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Ảnh hiện tại</label><br>
            <img src="uploads/<?= $row['hinh_anh'] ?>" width="100"><br><br>
            <input type="file" name="hinh_anh" class="form-control">
        </div>
        <div class="form-group">
            <label>Số lượng</label>
            <input type="number" name="so_luong" value="<?= $row['so_luong'] ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Giá tiền</label>
            <input type="number" name="gia_tien" value="<?= $row['gia_tien'] ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Danh mục</label>
            <input type="text" name="danh_muc" value="<?= $row['danh_muc'] ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trang_thai" class="form-control">
                <option value="Còn hàng" <?= ($row['trang_thai'] == 'Còn hàng') ? 'selected' : '' ?>>Còn hàng</option>
                <option value="Hết hàng" <?= ($row['trang_thai'] == 'Hết hàng') ? 'selected' : '' ?>>Hết hàng</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
        <a href="index.php" class="btn btn-default">🔙 Quay lại</a>
    </form>
</div>

</body>
</html>
