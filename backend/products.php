<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Lấy danh sách sản phẩm từ cơ sở dữ liệu
$result_san_pham = $conn->query("SELECT * FROM san_pham");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
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
    <!-- Buttons -->
    <div class="filters">
        <div>
            <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm sản phẩm</a>
            <a href="#" class="btn btn-info"><i class="fas fa-list"></i> DS nhập</a>
            <a href="#" class="btn btn-warning"><i class="fas fa-list"></i> DS xuất</a>
            <a href="#" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</a>
            <a href="#" class="btn btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <h3><i class="fas fa-box"></i> Danh sách sản phẩm</h3>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="5%"><input type="checkbox"></th>
                    <th width="10%">Hình ảnh</th>
                    <th width="15%">Mã sản phẩm</th>
                    <th width="20%">Tên sản phẩm</th>
                    <th width="10%">Số lượng</th>
                    <th width="15%">Trạng thái</th>
                    <th width="15%">Giá bán</th>
                    <th width="10%">Chọn chức năng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_san_pham->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <?php if (!empty($row['hinh_anh'])): ?>
                                <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Ảnh sản phẩm" style="max-width: 50px; max-height: 50px;">
                            <?php else: ?>
                                <img src="assets/images/no-image.png" alt="Không có ảnh" style="max-width: 50px; max-height: 50px;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['ma_san_pham']); ?></td>
                        <td><?php echo htmlspecialchars($row['ten_san_pham']); ?></td>
                        <td><?php echo htmlspecialchars($row['so_luong']); ?></td>
                        <td><?php echo htmlspecialchars($row['trang_thai']); ?></td>
                        <td><?php echo number_format($row['gia_tien'], 0, ',', '.') . ' VND'; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm <?php echo htmlspecialchars($row['ten_san_pham']); ?>? Hành động này không thể hoàn tác.');"><i class="fas fa-trash"></i> Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>