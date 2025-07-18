<?php
session_start();

// Xóa session
session_unset();
session_destroy();

// Xóa cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/");
}

// Lưu thông báo và chuyển hướng
$_SESSION['message'] = "Đăng xuất thành công";
header("Location: ../index.php");
exit();
?>