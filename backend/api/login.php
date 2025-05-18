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
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (assuming passwords are NOT plain text in DB)
            // If you are storing plain text passwords, this is insecure!
            // You should ideally use password_hash() and password_verify()
            if (password_verify($password, $user['password'])) { // !!! Replace with password_verify() if using password_hash()
                // Login successful
                session_start(); // Start session to store user info
                $_SESSION['user'] = [ // Store relevant user info in session
                    'id' => $user['id'],
                    'username' => $user['username'],
                ];

                $response = ['success' => true, 'message' => 'Đăng nhập thành công!', 'user' => ['username' => $user['username']]];
            } else {
                // Incorrect password
                $response = ['success' => false, 'message' => 'Sai mật khẩu.'];
            }
        } else {
            // User not found
            $response = ['success' => false, 'message' => 'Tên đăng nhập không tồn tại.'];
        }
        $stmt->close();
    }
}

echo json_encode($response);

$conn->close();
?> 