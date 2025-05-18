<?php
session_start();
if(isset($_SESSION['user'])) {
    header('Location: /quan-ly-don-hang/frontend/');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/quan-ly-don-hang/frontend/assets/css/base.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
        }
    </style>
</head>
<body>

<a href="/quan-ly-don-hang/frontend/" class="btn btn-primary back-home">
    <i class="bi bi-house-door"></i> Về trang chủ
</a>
<div class="login-container">
    <h2 class="text-center mb-4">Đăng nhập</h2>
    <div id="message" class="alert d-none"></div>
    <form id="loginForm">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
    </form>
    <p class="text-center mt-3">Chưa có tài khoản? <a href="/quan-ly-don-hang/frontend/register.php">Đăng ký ngay</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const jsonData = {};

        // Convert form data to JSON object
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        const messageDiv = document.getElementById('message');

        fetch('/quan-ly-don-hang/backend/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(jsonData)
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
            if (data.success) {
                messageDiv.classList.add('alert-success');
                messageDiv.textContent = data.message;
                // Lưu thông tin user vào session
                if(data.user) {
                    // Reload trang để cập nhật header
                    window.location.reload();
                }
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.classList.remove('d-none', 'alert-success');
            messageDiv.classList.add('alert-danger');
            messageDiv.textContent = 'Đã xảy ra lỗi trong quá trình đăng nhập.';
        });
    });
</script>

</body>
</html> 