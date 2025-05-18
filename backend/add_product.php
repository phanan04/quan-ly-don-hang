<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['them_san_pham'])) {
    $ma_san_pham = $_POST['ma_san_pham'];
    $ten_san_pham = $_POST['ten_san_pham'];
    $so_luong = $_POST['so_luong'];
    $gia_tien = $_POST['gia_tien'];
    $danh_muc = $_POST['danh_muc'];
    $trang_thai = $_POST['so_luong'] > 0 ? 'Còn hàng' : 'Hết hàng';
    
    // Xử lý upload ảnh
    $hinh_anh = "";
    if(isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
        $target_dir = "assets/images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["hinh_anh"]["name"], PATHINFO_EXTENSION));
        $new_filename = $ma_san_pham . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Kiểm tra định dạng file
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if(in_array($file_extension, $allowed_types)) {
            if(move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
                $hinh_anh = $target_file;
            } else {
                $msg = "❌ Lỗi khi upload ảnh!";
            }
        } else {
            $msg = "❌ Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF, WebP)!";
        }
    }
    
    if(empty($msg)) {
        // Kiểm tra mã sản phẩm đã tồn tại chưa
        $check = $conn->query("SELECT * FROM san_pham WHERE ma_san_pham = '$ma_san_pham'");
        if ($check->num_rows > 0) {
            $msg = "❌ Mã sản phẩm đã tồn tại!";
        } else {
            $sql = "INSERT INTO san_pham (ma_san_pham, ten_san_pham, so_luong, gia_tien, trang_thai, hinh_anh, danh_muc)
                    VALUES ('$ma_san_pham', '$ten_san_pham', $so_luong, $gia_tien, '$trang_thai', '$hinh_anh', '$danh_muc')";
            
            if ($conn->query($sql) === TRUE) {
                $msg = "✔️ Thêm sản phẩm thành công!";
            } else {
                $msg = "❌ Lỗi: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo">GHN</div>
    <div class="user-info">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="index.php"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php" class="active"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="main">
    <div class="form-container">
        <h3>Thêm sản phẩm mới</h3>
        
        <?php if ($msg): ?>
            <div class="alert <?php echo strpos($msg, '❌') !== false ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo $msg; ?>
            </div>
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

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Số lượng</label>
                        <input type="number" name="so_luong" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Giá tiền (VND)</label>
                        <input type="number" name="gia_tien" class="form-control" min="0" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Danh mục</label>
                <input type="text" name="danh_muc" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Hình ảnh sản phẩm</label>
                <input type="file" name="hinh_anh" class="form-control" accept="image/*" onchange="previewImage(this)">
                <img id="preview" class="preview-image">
            </div>

            <div class="form-actions">
                <button type="submit" name="them_san_pham" class="btn btn-success">Thêm sản phẩm</button>
                <a href="products.php" class="btn btn-danger">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
function previewImage(input) {
    var preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
</body>
</html> 