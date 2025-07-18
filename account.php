<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
if ($is_logged_in) {
    header("Location: index.php?message=Bạn đã đăng nhập");
    exit();
}
?>
  <!-- ---------------------------------------------------------------------------------------------------------------------thêm trên -->

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập/Đăng Ký</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
        }

        .auth-box {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .auth-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .auth-box .form-group {
            margin-bottom: 15px;
        }

        .auth-box .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .auth-box .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .auth-box button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .auth-box button:hover {
            background-color: #0056b3;
        }

        .auth-box .switch-form {
            text-align: center;
            margin-top: 15px;
        }

        .auth-box .switch-form a {
            color: #007bff;
            text-decoration: none;
        }

        .message,
        .error {
            text-align: center;
            margin-bottom: 15px;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header-chi-tiet-tour">
        <div class="container">
            <div class="inner-wrap">
                <button class="inner-menu-mobile">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="inner-logo"><a href="index.php">tour guide</a></div>
                <div class="menu-button">
                    <nav class="inner-menu">
                        <ul>
                            <li><a href="index.php" class="active">Home</a></li>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Popular Destinations</a></li>
                            <li><a href="#">Our Packages</a></li>
                            <li><a href="#">Help</a></li>
                        </ul>
                        <div class="inner-overlay"></div>
                    </nav>
                    <button class="inner-button"><a href="account.php">Sign In</a></button>
                </div>
            </div>
        </div>
    </header>

    <!-- Section: Auth -->
    <div class="section-23">
        <div class="container">
            <div class="inner-title">
                <h2>Đăng Nhập / Đăng Ký</h2>
                <p>Home / Đăng Nhập</p>
            </div>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-box">
            <!-- Hiển thị thông báo -->
            <?php if (isset($_GET['message'])): ?>
            <div class="message">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>

            <!-- Form Đăng Nhập -->
            <div id="login-form">
                <h2>Đăng Nhập</h2>
                <form action="back_end/login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật Khẩu</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="remember"> Ghi nhớ tôi</label>
                    </div>
                    <button type="submit">Đăng Nhập</button>
                </form>
                <div class="switch-form">
                    <p>Chưa có tài khoản? <a href="#" onclick="showRegisterForm()">Đăng Ký</a></p>
                </div>
            </div>

            <!-- Form Đăng Ký -->
            <div id="register-form" style="display: none;">
                <h2>Đăng Ký</h2>
                <form action="back_end/register.php" method="POST">
                    <div class="form-group">
                        <label for="username">Tên Người Dùng</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật Khẩu</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác Nhận Mật Khẩu</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit">Đăng Ký</button>
                </form>
                <div class="switch-form">
                    <p>Đã có tài khoản? <a href="#" onclick="showLoginForm()">Đăng Nhập</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="Section-12">
        <div class="container">
            <div class="inner-content">
                <div class="inner-column-1">
                    <h4>Ngôn ngữ</h4>
                    <select class="language">
                        <option>English (UK)</option>
                        <option>English (US)</option>
                        <option>Tiếng Việt</option>
                    </select>
                    <h4>Tiền tệ</h4>
                    <select class="money">
                        <option>U.S. Dollar ($)</option>
                        <option>Euro (€)</option>
                        <option>Japanese Yen (¥)</option>
                    </select>
                </div>
                <div class="inner-column-2">
                    <h4>Công ty</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Phòng báo chí</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                    </ul>
                </div>
                <div class="inner-column-3">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Điều khoản và điều kiện</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Sơ đồ trang</a></li>
                    </ul>
                </div>
                <div class="inner-column-4">
                    <h4>Phương thức thanh toán</h4>
                    <div class="payment-icons">
                        <a href="#"><img src="assets/images/footer-item-1.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-2.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-3.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-4.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-5.jpg" alt=""></a><br>
                        <a href="#"><img src="assets/images/footer-item-6.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-7.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-8.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-9.jpg" alt=""></a>
                        <a href="#"><img src="assets/images/footer-item-10.jpg" alt=""></a>
                    </div>
                    <h4>Công ty</h4>
                    <p>Trở thành hướng dẫn viên cho chúng tôi</p>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer-bottom">
        <p>Bản quyền 2021 Tour Guide. Mọi quyền được bảo lưu</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-pinterest"></i></a>
        </div>
    </footer>

    <!-- Box Contact -->
    <nav class="box-contact">
        <ul>
            <li><a href="#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-whatsapp"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
        </ul>
    </nav>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        function showRegisterForm() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        }
        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        }
    </script>
</body>

</html>

