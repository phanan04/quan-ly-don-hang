<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Lấy thông tin sản phẩm cần sửa
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Lấy thông tin sản phẩm từ database
$sql = "SELECT * FROM san_pham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit();
}

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_san_pham = $_POST['ma_san_pham'];
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia_tien = floatval($_POST['gia_tien']);
    $so_luong = intval($_POST['so_luong']);
    $trang_thai = $_POST['trang_thai'];
    $danh_muc = $_POST['danh_muc'] ?? $product['danh_muc'];
    
    // Xử lý upload ảnh
    $hinh_anh = $product['hinh_anh']; // Giữ ảnh cũ nếu không upload ảnh mới
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/products/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES['hinh_anh']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_path)) {
            // Xóa ảnh cũ nếu có
            if ($product['hinh_anh'] && file_exists($upload_dir . $product['hinh_anh'])) {
                unlink($upload_dir . $product['hinh_anh']);
            }
            $hinh_anh = $new_filename;
        }
    }
    
    // Cập nhật sản phẩm
    $sql = "UPDATE `san_pham` SET 
            `ma_san_pham` = ?, 
            `ten_san_pham` = ?, 
            `gia_tien` = ?, 
            `so_luong` = ?, 
            `trang_thai` = ?, 
            `hinh_anh` = ?,
            `danh_muc` = ?
            WHERE `id` = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiisssi", $ma_san_pham, $ten_san_pham, $gia_tien, $so_luong, $trang_thai, $hinh_anh, $danh_muc, $product_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
        header('Location: products.php');
        exit();
    } else {
        $error = "Có lỗi xảy ra khi cập nhật sản phẩm!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Sửa sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="ma_san_pham">Mã sản phẩm</label>
                                <input type="text" class="form-control" id="ma_san_pham" name="ma_san_pham" value="<?php echo htmlspecialchars($product['ma_san_pham']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="ten_san_pham">Tên sản phẩm</label>
                                <input type="text" class="form-control" id="ten_san_pham" name="ten_san_pham" value="<?php echo htmlspecialchars($product['ten_san_pham']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="gia_tien">Giá bán</label>
                                <input type="number" class="form-control" id="gia_tien" name="gia_tien" value="<?php echo $product['gia_tien']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="so_luong">Số lượng</label>
                                <input type="number" class="form-control" id="so_luong" name="so_luong" value="<?php echo $product['so_luong']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="trang_thai">Trạng thái</label>
                                <select class="form-control" id="trang_thai" name="trang_thai" required>
                                    <option value="Còn hàng" <?php echo $product['trang_thai'] == 'Còn hàng' ? 'selected' : ''; ?>>Còn hàng</option>
                                    <option value="Hết hàng" <?php echo $product['trang_thai'] == 'Hết hàng' ? 'selected' : ''; ?>>Hết hàng</option>
                                    <option value="Ngừng kinh doanh" <?php echo $product['trang_thai'] == 'Ngừng kinh doanh' ? 'selected' : ''; ?>>Ngừng kinh doanh</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="hinh_anh">Hình ảnh</label>
                                <?php if ($product['hinh_anh']): ?>
                                    <div class="mb-2">
                                        <img src="<?php echo htmlspecialchars($product['hinh_anh']); ?>" alt="Current image" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="hinh_anh" name="hinh_anh" accept="image/*">
                                <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
                            </div>

                            <div class="form-group">
                                <a href="products.php" class="btn btn-default">Quay lại</a>
                                <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
