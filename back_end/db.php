<?php
$host = "localhost";
$dbname = "tour_website_new"; // Cập nhật tên cơ sở dữ liệu
$username = "root";
$password = ""; // Thay nếu có mật khẩu khác

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>