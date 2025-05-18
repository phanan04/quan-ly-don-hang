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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đơn hàng</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<div class="sidebar text-primary">
    <div class="logo">GHN</div>
    <div class="user-info">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="index.php"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php" class="active"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="main">
    <div class="form-container">
        <h3>Tạo mới đơn hàng</h3>
        
        <form method="POST">
            <div class="form-group">
                <label>ID đơn hàng </label>
                <input type="text" name="ma_don_hang" class="form-control" placeholder="ID đơn hàng">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tên khách hàng</label>
                        <input type="text" name="ten_khach_hang" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Số điện thoại khách hàng</label>
                        <input type="text" name="sdt_khach_hang" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tên người bán</label>
                        <input type="text" name="ten_nguoi_ban" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Số điện thoại người bán</label>
                        <input type="text" name="sdt_nguoi_ban" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Địa chỉ khách hàng</label>
                <input type="text" name="dia_chi_khach_hang" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ngày làm đơn hàng</label>
                        <input type="date" name="ngay_tao" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Số lượng</label>
                        <input type="number" name="so_luong" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tên sản phẩm cần bán</label>
                        <select name="san_pham_id" class="form-control" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            <?php while ($sp = $result_san_pham->fetch_assoc()): ?>
                                <option value="<?= $sp['id'] ?>">
                                    <?= $sp['ten_san_pham'] ?> (SL: <?= $sp['so_luong'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tình trạng</label>
                        <select name="trang_thai" class="form-control" required>
                            <option value="">-- Chọn tình trạng --</option>
                            <option value="Chờ bàn giao">Chờ bàn giao</option>
                            <option value="Đã bàn giao">Đã bàn giao</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Mã sản phẩm</label>
                        <input type="text" name="ma_san_pham" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Ghi chú đơn hàng</label>
                        <textarea name="ghi_chu" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="tao_don" class="btn btn-success">Lưu lại</button>
                <a href="products.php" class="btn btn-danger">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>