<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// Hàm format trạng thái
function format_status($status) {
    switch($status) {
        case 'Hoàn':
            return 'Hoàn thành';
        case 'Chờ':
            return 'Chờ bàn giao';
        case 'Đã giao':
            return 'Đã bàn giao';
        default:
            return $status;
    }
}

// Tìm kiếm đơn hàng
$search_query = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search_query = $_POST['search'];
    // Updated query to join with chi_tiet_don_hang and san_pham
    $sql = "SELECT
                dh.*,
                ctdh.san_pham_id,
                ctdh.so_luong AS so_luong_ctdh, -- Alias to distinguish from potential old don_hang.so_luong
                ctdh.gia_tien_don_hang,
                sp.ten_san_pham
            FROM don_hang dh
            JOIN chi_tiet_don_hang ctdh ON dh.id = ctdh.don_hang_id
            JOIN san_pham sp ON ctdh.san_pham_id = sp.id
            WHERE dh.ma_don_hang LIKE '%" . $conn->real_escape_string($search_query) . "%'"; // Escape search query
    $result_don_hang = $conn->query($sql);
} else {
    // Default query to join with chi_tiet_don_hang and san_pham
    $sql = "SELECT
                dh.*,
                ctdh.san_pham_id,
                ctdh.so_luong AS so_luong_ctdh,
                ctdh.gia_tien_don_hang,
                sp.ten_san_pham
            FROM don_hang dh
            JOIN chi_tiet_don_hang ctdh ON dh.id = ctdh.don_hang_id
            JOIN san_pham sp ON ctdh.san_pham_id = sp.id";
    $result_don_hang = $conn->query($sql);
}

// Check if query failed
if ($result_don_hang === FALSE) {
    die("Lỗi truy vấn database: " . $conn->error); // Display error for debugging
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">GHN</div>
    <div class="user-info">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="#" class="active"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="main">
    <div class="topbar">
        <div class="stats">
            <span>Đơn nhập: <strong>5</strong></span>
            <span>Chờ bàn giao: <strong>5</strong></span>
            <span>Đã bàn giao - Đang giao: <strong>5</strong></span>
            <span>Hoàn Thành: <strong>5</strong></span>
        </div>
        <div class="search-bar">
            <form method="POST" style="display: inline;">
                <input type="text" name="search" class="form-control" style="margin: 0 5px 0 0" placeholder="Nhập mã đơn " value="<?php echo htmlspecialchars($search_query); ?>">
            </form>
            <a href="add.php" class="btn btn-primary"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
        </div>
    </div>

    <div class="filters">
        <div>
            <select class="form-control" style="display: inline-block; width: 150px;">
                <option>Tất cả trạng thái</option>
            </select>
            <select class="form-control" style="display: inline-block; width: 150px; margin-left: 10px;">
                <option>Tất cả</option>
            </select>
        </div>
        <div>
            <input type="date" class="form-control" value="2025-04-04" style="display: inline-block; width: 150px;">
            <span style="margin: 0 10px;">Đến</span>
            <input type="date" class="form-control" value="2025-04-10" style="display: inline-block; width: 150px;">
            <span style="margin-left: 15px;">Hiện tại: <strong>5/5 đơn hàng</strong></span>
        </div>
    </div>

    <div class="table-container">
        <h3><i class="fas fa-list"></i> Danh sách đơn hàng</h3>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="5%"><input type="checkbox"></th>
                    <th width="5%">STT</th>
                    <th width="15%">Mã đơn</th>
                    <th width="20%">Sản phẩm</th>
                    <th width="10%">Số lượng</th>
                    <th width="15%">Tổng phí</th>
                    <th width="15%">Thu hộ/COD</th>
                    <th width="10%">Trạng thái</th>
                    <th width="15%">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php $stt = 1; while ($row = $result_don_hang->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><?php echo $stt++ ?></td>
                    <td><?php echo $row['ma_don_hang'] ?></td>
                    <td><?php echo $row['ten_san_pham'] ?></td>
                    <td><?php echo $row['so_luong_ctdh'] ?></td>
                    <td><?php echo number_format($row['tong_phi'], 0, ',', '.') ?> VND</td>
                    <td><?php echo number_format($row['thu_ho'], 0, ',', '.') ?> VND</td>
                    <td><?php echo format_status($row['trang_thai']) ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id'] ?>" class="btn btn-default btn-sm"><i class="fas fa-edit"></i> Chỉnh sửa</a>
                        <a href="delete.php?id=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa đơn hàng này?')"><i class="fas fa-trash"></i> Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>