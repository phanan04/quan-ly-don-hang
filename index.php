<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
$result = $conn->query("SELECT * FROM san_pham");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trang quản lý sản phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <style>
        body {
            font-family: Arial;
        }
        .sidebar {
            height: 100vh;
            background: #222;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: #ccc;
            display: block;
            padding: 10px 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #444;
            color: white;
        }
        .main {
            padding: 20px;
        }
        .topbar {
            padding: 10px 20px;
            background: #f5f5f5;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-sm-3 col-md-2 sidebar">
            <h4 class="text-center">🛒 Quản lý</h4>
            <a href="index.php">📦 Sản phẩm</a>
            <a href="add.php">➕ Thêm sản phẩm</a>
            <a href="logout.php">🚪 Đăng xuất</a>
        </div>

        <!-- Nội dung chính -->
        <div class="col-sm-9 col-md-10 main">
            <div class="topbar">
                <span>👤 Xin chào, <strong><?= $_SESSION['user'] ?></strong></span>
            </div>

            <h3>📦 Danh sách sản phẩm</h3>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên</th>
                        <th>Ảnh</th>
                        <th>Số lượng</th>
                        <th>Giá tiền</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['ma_san_pham'] ?></td>
                        <td><?= $row['ten_san_pham'] ?></td>
                        <td>
                            <?php if ($row['hinh_anh']): ?>
                                <img src="uploads/<?= $row['hinh_anh'] ?>" width="60">
                            <?php endif; ?>
                        </td>
                        <td><?= $row['so_luong'] ?></td>
                        <td><?= number_format($row['gia_tien'], 0, ',', '.') ?>đ</td>
                        <td><?= $row['danh_muc'] ?></td>
                        <td><?= $row['trang_thai'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">✏️</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn xoá?')">🗑️</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
