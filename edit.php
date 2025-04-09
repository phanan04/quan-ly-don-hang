<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$id = $_GET['id'];
$msg = "";

$sql = "SELECT dh.*, sp.ten_san_pham, sp.so_luong AS sp_so_luong FROM don_hang dh JOIN san_pham sp ON dh.san_pham_id = sp.id WHERE dh.id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if (!$row) {
    die("Không tìm thấy đơn hàng");
}

$result_san_pham = $conn->query("SELECT * FROM san_pham WHERE so_luong > 0 AND trang_thai = 'Còn hàng'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $san_pham_id = $_POST['san_pham_id'];
    $so_luong = $_POST['so_luong'];
    $trang_thai = $_POST['trang_thai'];

    // Lấy thông tin sản phẩm mới
    $sp_result = $conn->query("SELECT * FROM san_pham WHERE id = $san_pham_id");
    $san_pham = $sp_result->fetch_assoc();

    if ($san_pham) {
        // Tính toán số lượng cần điều chỉnh
        $old_san_pham_id = $row['san_pham_id'];
        $old_so_luong = $row['so_luong'];
        
        if ($san_pham_id == $old_san_pham_id) {
            $so_luong_diff = $so_luong - $old_so_luong;
            $available_so_luong = $san_pham['so_luong'] - $so_luong_diff;
        } else {
            $available_so_luong = $san_pham['so_luong'] - $so_luong;
            // Trả lại số lượng cho sản phẩm cũ
            $conn->query("UPDATE san_pham SET so_luong = so_luong + $old_so_luong WHERE id = $old_san_pham_id");
        }

        if ($available_so_luong >= 0) {
            $tong_phi = $san_pham['gia_tien'] * $so_luong;
            $thu_ho = $tong_phi;

            $sql_update = "UPDATE don_hang SET
                san_pham_id = $san_pham_id,
                so_luong = $so_luong,
                tong_phi = $tong_phi,
                thu_ho = $thu_ho,
                trang_thai = '$trang_thai'
                WHERE id = $id";

            if ($conn->query($sql_update) === TRUE) {
                // Cập nhật số lượng sản phẩm trong kho
                $conn->query("UPDATE san_pham SET so_luong = so_luong - $so_luong WHERE id = $san_pham_id");
                $msg = "✔️ Cập nhật đơn hàng thành công!";
                $row = $conn->query("SELECT dh.*, sp.ten_san_pham, sp.so_luong AS sp_so_luong FROM don_hang dh JOIN san_pham sp ON dh.san_pham_id = sp.id WHERE dh.id = $id")->fetch_assoc();
            } else {
                $msg = "❌ Lỗi: " . $conn->error;
            }
        } else {
            $msg = "❌ Số lượng không đủ trong kho!";
        }
    } else {
        $msg = "❌ Sản phẩm không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa đơn hàng</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">GHN</div>
    <div class="user-info">
        <img src="https://via.placeholder.com/60" alt="User">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="index.php"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="#"><i class="fas fa-store"></i> Quản lý cửa hàng</a>
    <a href="#"><i class="fas fa-money-bill"></i> COD & đối soát</a>
    <a href="#"><i class="fas fa-question-circle"></i> Yêu cầu hỗ trợ</a>
    <a href="#"><i class="fas fa-users"></i> Phân quyền</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    <div class="sidebar-footer">Phần mềm 0.1</div>
</div>

<div class="main">
    <div class="form-container">
        <h3><i class="fas fa-edit"></i> Chỉnh sửa đơn hàng</h3>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Mã đơn hàng</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['ma_don_hang']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Chọn sản phẩm</label>
                <select name="san_pham_id" class="form-control" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    <?php while ($sp = $result_san_pham->fetch_assoc()): ?>
                        <option value="<?php echo $sp['id'] ?>" <?= ($sp['id'] == $row['san_pham_id']) ? 'selected' : '' ?>>
                            <?php echo $sp['ten_san_pham'] ?> (SL: <?php echo $sp['so_luong'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Số lượng</label>
                <input type="number" name="so_luong" value="<?= $row['so_luong'] ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control">
                    <option value="Chờ bàn giao" <?= ($row['trang_thai'] == 'Chờ bàn giao') ? 'selected' : '' ?>>Chờ bàn giao</option>
                    <option value="Đã bàn giao" <?= ($row['trang_thai'] == 'Đã bàn giao') ? 'selected' : '' ?>>Đã bàn giao</option>
                    <option value="Đang giao" <?= ($row['trang_thai'] == 'Đang giao') ? 'selected' : '' ?>>Đang giao</option>
                    <option value="Hoàn" <?= ($row['trang_thai'] == 'Hoàn') ? 'selected' : '' ?>>Hoàn Thành</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
            <a href="index.php" class="btn btn-default"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </form>
    </div>
</div>

</body>
</html>