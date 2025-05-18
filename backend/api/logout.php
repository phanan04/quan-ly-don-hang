<?php
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Xóa cookie session nếu có
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Hủy session
session_destroy();

// Chuyển hướng về trang chủ
header('Location: /quan-ly-don-hang/frontend/');
exit();
?> 