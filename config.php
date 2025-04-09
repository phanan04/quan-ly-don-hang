<?php
$servername = "localhost";
$username = "root";
$password = "Anninh@2004";
$dbname = "shop_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
