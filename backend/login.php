<?php
session_start();
include 'config.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $msg = "❌ Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body style="padding:30px">
<div class="container">
    <h3>🔐 Đăng nhập</h3>
    <?php if ($msg): ?>
        <div class="alert alert-danger"><?= $msg ?></div>
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
        <button type="submit" class="btn btn-primary">Đăng nhập</button>
        <a href="register.php" class="btn btn-link">Chưa có tài khoản?</a>
    </form>
</div>
</body>
</html>
