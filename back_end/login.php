<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        header("Location: ../account.php?error=Mọi trường đều bắt buộc");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        if ($remember) {
            $token = bin2hex(random_bytes(16));
            setcookie('remember_token', $token, time() + 30 * 24 * 3600, "/");
        }

        // Lưu thông báo và chuyển hướng
        $_SESSION['message'] = "Đăng nhập thành công";
        header("Location: ../account.php");
        exit();
    } else {
        header("Location: ../account.php?error=Email hoặc mật khẩu không đúng");
        exit();
    }
} else {
    header("Location: ../account.php?error=Phương thức không hợp lệ");
    exit();
}
?>