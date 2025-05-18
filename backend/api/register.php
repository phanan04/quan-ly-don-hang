<?php
header('Content-Type: application/json');

require_once '../config.php'; // Adjust path as needed

$response = ['success' => false, 'message' => 'Yêu cầu không hợp lệ.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Fallback for form-data if json_decode fails or is empty
    if (json_decode(file_get_contents('php://input'), true) === null || empty($input)) {
        $input = $_POST;
    }

    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        $response = ['success' => false, 'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu.'];
    } else {
        // Check if username already exists
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại.'];
        } else {
            // Hash the password before storing
            // Using PASSWORD_DEFAULT which is the recommended algorithm
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $default_role = 'user'; // Set a default role for new users

            // Insert new user into database
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $hashed_password, $default_role);

            if ($stmt_insert->execute()) {
                $response = ['success' => true, 'message' => 'Đăng ký thành công!'];
            } else {
                $response = ['success' => false, 'message' => 'Lỗi khi đăng ký người dùng: ' . $stmt_insert->error];
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

echo json_encode($response);

$conn->close();
?> 