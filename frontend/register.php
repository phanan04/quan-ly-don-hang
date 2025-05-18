<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/quan-ly-don-hang/frontend/assets/css/base.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2 class="text-center mb-4">Đăng ký tài khoản mới</h2>
    <div id="message" class="alert d-none"></div>
    <form id="registerForm">
        <div class="mb-3">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <!-- Add confirm password field if needed -->
        <!-- <div class="mb-3">
            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div> -->
        <button type="submit" class="btn btn-success w-100">Đăng ký</button>
    </form>
    <p class="text-center mt-3">Đã có tài khoản? <a href="/quan-ly-don-hang/frontend/login.php">Đăng nhập</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const jsonData = {};

        // Convert form data to JSON object
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        // You might want to add client-side password confirmation check here

        const messageDiv = document.getElementById('message');

        fetch('/quan-ly-don-hang/backend/api/register.php', {
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
                messageDiv.textContent = data.message + ' Vui lòng đăng nhập.';
                // Optionally redirect to login page after successful registration
                // window.location.href = '/quan-ly-don-hang/frontend/login.php';
            } else {
                messageDiv.classList.add('alert-danger');
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.classList.remove('d-none', 'alert-success');
            messageDiv.classList.add('alert-danger');
            messageDiv.textContent = 'Đã xảy ra lỗi trong quá trình đăng ký.';
        });
    });
</script>

</body>
</html> 