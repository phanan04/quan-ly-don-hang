<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$msg = "";
$result_san_pham = $conn->query("SELECT * FROM san_pham WHERE so_luong > 0 AND trang_thai = 'Còn hàng'");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tao_don'])) {
    $san_pham_id = $_POST['san_pham_id'];
    $so_luong = $_POST['so_luong'];
    
    $result = $conn->query("SELECT * FROM san_pham WHERE id = $san_pham_id");
    $san_pham = $result->fetch_assoc();
    
    if ($san_pham && $san_pham['so_luong'] >= $so_luong) {
        $ma_don_hang = "DH" . time();
        $tong_phi = $san_pham['gia_tien'] * $so_luong;
        $thu_ho = $tong_phi;
        $trang_thai = "Chờ bàn giao";
        $ngay_tao = date("Y-m-d H:i:s");

        $sql = "INSERT INTO don_hang (ma_don_hang, san_pham_id, so_luong, tong_phi, thu_ho, trang_thai, ngay_tao)
                VALUES ('$ma_don_hang', $san_pham_id, $so_luong, $tong_phi, $thu_ho, '$trang_thai', '$ngay_tao')";
        
        if ($conn->query($sql) === TRUE) {
            $new_so_luong = $san_pham['so_luong'] - $so_luong;
            $conn->query("UPDATE san_pham SET so_luong = $new_so_luong WHERE id = $san_pham_id");
            $msg = "✔️ Tạo đơn hàng thành công!";
        } else {
            $msg = "❌ Lỗi: " . $conn->error;
        }
    } else {
        $msg = "❌ Số lượng không đủ hoặc sản phẩm không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tạo đơn hàng</title>
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
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="#" class="active"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="#"><i class="fas fa-store"></i> Quản lý cửa hàng</a>
    <a href="#"><i class="fas fa-money-bill"></i> COD & đối soát</a>
    <a href="#"><i class="fas fa-question-circle"></i> Yêu cầu hỗ trợ</a>
    <a href="#"><i class="fas fa-users"></i> Phân quyền</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    <div class="sidebar-footer">Phần mềm 0.1</div>
</div>

<div class="main">
    <div class="form-container">
        <h3><i class="fas fa-truck"></i> Tạo đơn hàng từ sản phẩm</h3>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Chọn sản phẩm</label>
                <select name="san_pham_id" class="form-control" required>
                    <option value="">-- Chọn sản phẩm --</option>
                    <?php while ($sp = $result_san_pham->fetch_assoc()): ?>
                        <option value="<?php echo $sp['id'] ?>"><?php echo $sp['ten_san_pham'] ?> (SL: <?php echo $sp['so_luong'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Số lượng</label>
                <input type="number" name="so_luong" class="form-control" placeholder="Nhập số lượng" required>
            </div>
            <button type="submit" name="tao_don" class="btn btn-primary"><i class="fas fa-truck"></i> Tạo đơn</button>
            <a href="index.php" class="btn btn-default"><i class="fas fa-arrow-left"></i> Quay lại</a>
        </form>
    </div>
</div>

</body>
</html>