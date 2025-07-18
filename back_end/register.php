<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra dữ liệu
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: ../account.php?error=Mọi trường đều bắt buộc");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../account.php?error=Mật khẩu xác nhận không khớp");
        exit();
    }

    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        header("Location: ../account.php?error=Email đã được sử dụng");
        exit();
    }

    // Mã hóa mật khẩu và lưu vào cơ sở dữ liệu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashed_password])) {
        header("Location: ../account.php?message=Đăng ký thành công, vui lòng đăng nhập");
        exit();
    } else {
        header("Location: ../account.php?error=Đăng ký thất bại, vui lòng thử lại");
        exit();
    }
} else {
    header("Location: ../account.php?error=Phương thức không hợp lệ");
    exit();
}
?>