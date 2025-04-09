<?php
include 'config.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $msg = "⚠️ Tên đăng nhập đã tồn tại!";
    } else {
        $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
        $msg = "✅ Đăng ký thành công! <a href='login.php'>Đăng nhập</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body style="padding:30px">
<div class="container">
    <h3>📝 Đăng ký tài khoản</h3>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Đăng ký</button>
        <a href="login.php" class="btn btn-default">Quay lại đăng nhập</a>
    </form>
</div>
</body>
</html>
