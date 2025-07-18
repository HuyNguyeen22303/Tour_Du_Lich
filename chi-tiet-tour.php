<?php
session_start();
include 'back_end/db.php';

// Kiểm tra đăng nhập
$is_logged_in = isset($_SESSION['user_id']);

// Lấy ID tour từ URL (mặc định là 1 nếu không có)
$tour_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

try {
    // Truy vấn thông tin tour với cột 'tour_id'
    $sql_tour = "SELECT tour_id, title, description, location, price, duration, activities, includes, not_includes, max_people, rating, review_count FROM tours WHERE tour_id = :tour_id";
    $stmt_tour = $conn->prepare($sql_tour);
    $stmt_tour->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt_tour->execute();
    $tour = $stmt_tour->fetch(PDO::FETCH_ASSOC);

    if (!$tour) {
        die("Tour không tồn tại.");
    }

    // Tạo danh sách ảnh từ thư mục imagesTour dựa trên location
    $location = strtolower(str_replace(' ', '', $tour['location']));
    $tour_images = [
        ['image_path' => "assets/imagesTour/{$location}.jpg"],
        ['image_path' => "assets/imagesTour/{$location}1.jpg"],
        ['image_path' => "assets/imagesTour/{$location}2.jpg"],
        ['image_path' => "assets/imagesTour/{$location}3.jpg"],
        ['image_path' => "assets/imagesTour/{$location}4.jpg"],
        ['image_path' => "assets/imagesTour/{$location}5.jpg"]
    ];

    // Lọc chỉ những ảnh tồn tại
    $valid_images = [];
    foreach ($tour_images as $image) {
        if (file_exists($image['image_path']) && !empty($image['image_path'])) {
            $valid_images[] = $image;
        }
    }
    $tour_images = $valid_images; // Gán lại mảng chỉ chứa ảnh hợp lệ

    // Debug mảng $tour_images để kiểm tra đường dẫn
    echo "<!-- Debug tour_images: " . print_r($tour_images, true) . " -->";

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}


    // Truy vấn cho Section-15: Featured Destinations (lấy top 5 tour theo rating)
    try {
        $sql_featured = "SELECT tour_id, title, location, duration, price, rating, review_count 
                        FROM tours 
                        WHERE rating >= 4 
                        ORDER BY rating DESC 
                        LIMIT 5";
        $stmt_featured = $conn->prepare($sql_featured);
        $stmt_featured->execute();
        $featured_tours = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);

        // Thêm đường dẫn ảnh cho mỗi tour
        foreach ($featured_tours as &$ftour) {
            $location = strtolower(str_replace(' ', '', $ftour['location']));
            $image_path = "assets/imagesTour/{$location}.jpg";
            $ftour['image_path'] = file_exists($image_path) ? $image_path : "assets/imagesTour/default.jpg";
        }

    } catch (PDOException $e) {
        die("Lỗi truy vấn Featured Tours: " . $e->getMessage());
    }

    // Truy vấn cho Section-16: Related Tours in 
    try {
        $specific_location = "Dubai"; // Cố định location là Dubai
        $sql_related = "SELECT t.tour_id, t.title, t.location, t.duration, t.price, t.rating, t.review_count, ti.image_path 
                        FROM tours t 
                        LEFT JOIN tour_images ti ON t.tour_id = ti.tour_id 
                        WHERE t.location = :location AND t.tour_id != :tour_id 
                        LIMIT 5";
        $stmt_related = $conn->prepare($sql_related);
        $stmt_related->bindParam(':location', $specific_location, PDO::PARAM_STR);
        $stmt_related->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt_related->execute();
        $related_tours = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

        // Xử lý ảnh mặc định nếu không có ảnh
        foreach ($related_tours as &$rtour) {
            $rtour['image_path'] = !empty($rtour['image_path']) && file_exists($rtour['image_path']) 
                ? $rtour['image_path'] 
                : "assets/imagesTour/default.jpg";
        }

    } catch (PDOException $e) {
        die("Lỗi truy vấn Related Tours: " . $e->getMessage());
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chi tiết tour</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .inner-form {
            height: auto !important;
            opacity: 1 !important;
            overflow: visible !important;
        }

        .booking-form .form-group,
        .booking-form .total-section,
        .booking-form button {
            display: block !important;
        }

        .booking-form .form-group input,
        .booking-form .form-group label,
        .booking-form .total-section span,
        .booking-form button i {
            display: block !important;
            visibility: visible !important;
        }

        .sign-out {
            background-color: #ffca28;
            color: black;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 350px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-content h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .modal-content p {
            color: #666;
            margin-bottom: 20px;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            color: #999;
            border: none;
            background: none;
            cursor: pointer;
        }

        .modal-close:hover {
            color: #000;
        }

        .modal-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .modal-button:hover {
            background-color: #0056b3;
        }

        /* Đảm bảo ảnh trong swiper hiển thị đúng */
        .swiper-slide img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .inner-images-thumb .swiper-slide img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }

        /* Ẩn Swiper nếu không có ảnh */
        .swiper-box-images-main, .swiper-box-images-thumb {
            display: <?php echo empty($tour_images) ? 'none' : 'block'; ?>;
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
                <div class="inner-logo"><a href="index.php" class="">tour guide</a></div>
                <div class="menu-button">
                    <nav class="inner-menu">
                        <ul class="">
                            <li class=""><a href="index.php" class="active">Home</a></li>
                            <li class=""><a href="#" class="">About Us</a></li>
                            <li class=""><a href="listing-tour.php" class="">Popular Destinations</a></li>
                            <li class=""><a href="#" class="">Our Packages</a></li>
                            <li class=""><a href="#" class="">Help</a></li>
                        </ul>
                        <div class="inner-overlay"></div>
                    </nav>
                    <?php if ($is_logged_in): ?>
                        <button class="sign-out" onclick="window.location.href='back_end/logout.php'">Sign out</button>
                    <?php else: ?>
                        <button class="inner-button"><a href="account.php">Sign In</a></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header -->

    <!-- Section 13 -->
    <div class="section-13">
        <div class="container">
            <div class="inner-wrap">
                <h1 id="tour-title"><?php echo htmlspecialchars($tour['title']); ?></h1>

                <div class="inner-local-star">
                    <span class="local">
                        <a href=""><i class="fa-solid fa-location-dot"></i>
                            <?php echo htmlspecialchars($tour['location'] ?? 'Chưa có vị trí'); ?></a>
                    </span>
                    <span> | </span>
                    <span class="star">
                        <a href="">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            (<?php echo $tour['review_count'] ?? '0'; ?> reviews)
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 13 -->

    <!-- Section 14 -->
    <div class="section-14" data-aos="fade-up" data-aos-duration="800">
        <div class="container">
            <div class="inner-wrap">
                <div class="inner-left">
                    <div class="box-images">
                        <div class="inner-images-main">
                            <div class="swiper swiper-box-images-main">
                                <div class="swiper-wrapper">
                                    <?php if (!empty($tour_images)): ?>
                                        <?php foreach ($tour_images as $index => $image): ?>
                                            <div class="swiper-slide">
                                                <div class="inner-image">
                                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Tour Image <?php echo $index + 1; ?>" loading="lazy">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="swiper-button-next swiper-button-next-main"></div>
                                <div class="swiper-button-prev swiper-button-prev-main"></div>
                            </div>
                        </div>
                        <div class="inner-images-thumb">
                            <div class="swiper swiper-box-images-thumb">
                                <div class="swiper-wrapper">
                                    <?php if (!empty($tour_images)): ?>
                                        <?php foreach ($tour_images as $index => $image): ?>
                                            <div class="swiper-slide">
                                                <div class="inner-image">
                                                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Thumbnail <?php echo $index + 1; ?>" loading="lazy">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="inner-info-list">
                        <div class="inner-info-item">
                            <span class="info-text-title"><i class="fa-solid fa-ban"></i> Free cancellation</span>
                            <span class="info-text-desc">Cancel up to 24 hours in advance to receive a full refund</span>
                        </div>
                        <div class="inner-info-item">
                            <span class="info-text-title"> <i class="fa-solid fa-pump-medical"></i> Health precautions</span>
                            <span class="info-text-desc">Special health and safety measures apply. Learn more</span>
                        </div>
                        <div class="inner-info-item">
                            <span class="info-text-title"><i class="fa-solid fa-mobile-screen-button"></i> Mobile ticketing</span>
                            <span class="info-text-desc">Use your phone or print your voucher</span>
                        </div>
                        <div class="inner-info-item">
                            <span class="info-text-title"> <i class="fa-solid fa-clock-rotate-left"></i> Duration
                                <?php echo htmlspecialchars($tour['duration'] ?? '3.5'); ?> hours</span>
                            <span class="info-text-desc">Check availability to see starting times.</span>
                        </div>
                        <div class="inner-info-item">
                            <span class="info-text-title"> <i class="fa-solid fa-plane-circle-check"></i> Instant confirmation</span>
                            <span class="info-text-desc">Don’t wait for the confirmation!</span>
                        </div>
                        <div class="inner-info-item">
                            <span class="info-text-title"> <i class="fa-solid fa-headset"></i> Live tour guide in English</span>
                            <span class="info-text-desc">English</span>
                        </div>
                    </div>
                    <!-- Phần Description -->
                    <div class="desc-inner-left">
                        <h2 class="">Description</h2>
                        <div class="desc-list">
                            <div class="desc-item">
                                <?php echo nl2br(htmlspecialchars($tour['description'])); ?>
                            </div>
                        </div>
                    </div>
                    <!-- Phần Activity, Includes, Safety, Details -->
                    <div class="act-inner-left">
                        <div class="act-list">
                            <div class="act-item">
                                <h2>Activity</h2>
                                <h4>What you will do</h4>
                                <div class="act-item-list">
                                    <ul class="">
                                        <?php
                                        $activities = explode(',', $tour['activities'] ?? '');
                                        foreach ($activities as $activity):
                                            if (trim($activity) !== ''): ?>
                                                <li class=""><?php echo htmlspecialchars(trim($activity)); ?></li>
                                            <?php endif;
                                        endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="act-item-1-2">
                                <h2 class="">What is included / Not included</h2>
                                <div class="act-item-list-1-2">
                                    <div class="act-item-list-1">
                                        <h4 class="">Includes</h4>
                                        <ul class="">
                                            <?php
                                            $includes = explode(',', $tour['includes'] ?? '');
                                            foreach ($includes as $include):
                                                if (trim($include) !== ''): ?>
                                                    <li class=""><?php echo htmlspecialchars(trim($include)); ?></li>
                                                <?php endif;
                                            endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="act-item-list-2">
                                        <h4 class="">Not Includes</h4>
                                        <ul class="">
                                            <?php
                                            $not_includes = explode(',', $tour['not_includes'] ?? '');
                                            foreach ($not_includes as $not_include):
                                                if (trim($not_include) !== ''): ?>
                                                    <li class=""><?php echo htmlspecialchars(trim($not_include)); ?></li>
                                                <?php endif;
                                            endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="act-item">
                                <h2>Safety</h2>
                                <h4>Health precautions</h4>
                                <div class="act-item-list">
                                    <ul class="">
                                        <li class="">All required protective equipment is provided</li>
                                        <li class="">All areas that customers touch are frequently cleaned</li>
                                        <li class="">You must keep social distance while in vehicles</li>
                                        <li class="">The number of visitors is limited to reduce crowds</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="act-item-1-2-3">
                                <h2 class="">Details</h2>
                                <div class="act-item-list-1-2-3">
                                    <div class="act-item-list-1">
                                        <h4 class="">Language</h4>
                                        <ul class="">
                                            <li class="">English</li>
                                            <li class="">French</li>
                                        </ul>
                                    </div>
                                    <div class="act-item-list-2">
                                        <h4 class="">Duration</h4>
                                        <ul class="">
                                            <li class=""><?php echo htmlspecialchars($tour['duration'] ?? '3.5'); ?> hours</li>
                                        </ul>
                                    </div>
                                    <div class="act-item-list-3">
                                        <h4 class="">Number of people</h4>
                                        <ul class="">
                                            <li class=""><?php echo htmlspecialchars($tour['max_people'] ?? '5'); ?> People</li>
                                        </ul>
                                    </div>
                                </div>
                                <h4 class="">Meeting point address</h4>
                                <ul class="">
                                    <li class="">Meet your guide inside the west entrance of Altab Ali Park (Whitechapel Road). It's opposite the entrance to Aldgate East Tube Station and the Whitechapel Gallery. Look for a guide wearing SMT attire and holding a red SMT flag</li>
                                </ul>
                                <a href="https://www.google.com/maps/place/...">Open in Google Maps</a>
                                <br />
                                <br />
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18..." width="100%" height="340px" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="inner-right">
                    <div class="box-tour-detail">
                        <div class="inner-title-main">Booking</div>
                        <?php if ($is_logged_in): ?>
                            <form action="back_end/save_booking.php" method="POST" class="inner-form expanded">
                                <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
                                <div class="inner-group">
                                    <label for="from_date" class="inner-label">From</label>
                                    <input type="date" name="from_date" class="inner-group" required>
                                </div>
                                <div class="inner-group">
                                    <label for="to_date" class="inner-label">To</label>
                                    <input type="date" name="to_date" class="inner-group" required>
                                </div>
                                <div class="inner-group">
                                    <label for="guests" class="inner-label">No. of guest</label>
                                    <input type="number" name="guests" id="guests" min="1" max="<?php echo htmlspecialchars($tour['max_people'] ?? 5); ?>" required>
                                </div>
                                <div class="inner-meta">
                                    <div class="inner-total">
                                        <div class="inner-total-label">subtotal</div>
                                        <div class="inner-total-price" id="tourPrice">
                                            <?php echo number_format($tour['price'] ?? 78.90, ); ?> $
                                        </div>
                                    </div>
                                    <button type="submit" class="booking-button">
                                        Confirm Booking
                                    </button>
                                    <button type="button" class="">
                                        <i class="fa-regular fa-heart"></i> Save to wishlist
                                    </button>
                                    <button type="button" class="">
                                        <i class="fa-solid fa-share"></i> Share the activity
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="inner-form expanded" style="display: block;">
                                <div class="inner-meta">
                                    <button type="button" class="booking-button" onclick="showLoginModal()">
                                        Confirm Booking
                                    </button>
                                    <button type="button" class="">
                                        <i class="fa-regular fa-heart"></i> Save to wishlist
                                    </button>
                                    <button type="button" class="">
                                        <i class="fa-solid fa-share"></i> Share the activity
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 14 -->

    <!-- Modal cho thông báo login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="hideLoginModal()">×</button>
            <h2>Sign In</h2>
            <p>Please log in to book this tour.</p>
            <button class="modal-button" onclick="window.location.href='account.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'">Sign In</button>
        </div>
    </div>

    <!-- Modal cho thông báo booking thành công -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h2>Success!</h2>
            <p>Your booking has been successfully completed!</p>
            <button id="closeModalBtn">Close</button>
        </div>
    </div>

 
    <!-- Section 15 -->
    <div class="section-15" data-aos="fade-up" data-aos-duration="800">
        <div class="container">
            <h2 class="inner-title">Featured Destinations</h2>
            <div class="box-icon">
                <div class="icon">
                    <a class="lessthan-15" href="#"><i class="fa-regular fa-less-than"></i></a>
                </div>
                <div class="icon">
                    <a class="greaterthan-15" href="#"><i class="fa-regular fa-greater-than"></i></a>
                </div>
            </div>
        </div>
        <div class="inner-trip">
            <?php if (!empty($featured_tours)): ?>
                <?php foreach ($featured_tours as $ftour): ?>
                    <div class="box box-1">
                        <a href="chi-tiet-tour.php?id=<?php echo $ftour['tour_id']; ?>">
                            <img class="inner-image" src="<?php echo htmlspecialchars($ftour['image_path']); ?>" alt="<?php echo htmlspecialchars($ftour['title']); ?>">
                        </a>
                        <h3 class="inner-title"><?php echo htmlspecialchars($ftour['title']); ?></h3>
                        <div class="inner-icon"><i class="fa-solid fa-clock"></i> Duration <?php echo htmlspecialchars($ftour['duration']); ?> hours</div>
                        <div class="inner-icon"><i class="fa-solid fa-car-side"></i> Transport Facility</div>
                        <div class="inner-icon"><i class="fa-solid fa-people-group"></i> Family Plan</div>
                        <div class="inner-wrap2">
                            <div class="star">
                                <?php
                                $rating = round($ftour['rating']);
                                for ($i = 1; $i <= 5; $i++):
                                    $star_class = ($i <= $rating) ? 'fa-solid' : 'fa-regular';
                                ?>
                                    <i class="<?php echo $star_class; ?> fa-star s<?php echo $i; ?>"></i>
                                <?php endfor; ?>
                                <p class="inner-review"><?php echo htmlspecialchars($ftour['review_count']); ?> reviews</p>
                            </div>
                            <div class="price">
                                <div class="inner-price">$<?php echo number_format($ftour['price'], 2); ?></div>
                                <p class="inner-person">per person</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured tours available.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- End Section 15 -->

    <!-- Section 16 -->
    <div class="section-16" data-aos="fade-up" data-aos-duration="800">
        <div class="container">
            <h2 class="inner-title">Related Tours in Dubai</h2>
            <div class="box-icon">
                <div class="icon">
                    <a class="lessthan-16" href="#"><i class="fa-regular fa-less-than"></i></a>
                </div>
                <div class="icon">
                    <a class="greaterthan-16" href="#"><i class="fa-regular fa-greater-than"></i></a>
                </div>
            </div>
        </div>
        <div class="inner-trip">
            <?php if (!empty($related_tours)): ?>
                <?php foreach ($related_tours as $rtour): ?>
                    <div class="box box-1">
                        <a href="chi-tiet-tour.php?id=<?php echo $rtour['tour_id']; ?>">
                            <img class="inner-image" src="<?php echo htmlspecialchars($rtour['image_path']); ?>" alt="<?php echo htmlspecialchars($rtour['title']); ?>">
                        </a>
                        <h3 class="inner-title"><?php echo htmlspecialchars($rtour['title']); ?></h3>
                        <div class="inner-icon"><i class="fa-solid fa-clock"></i> Duration <?php echo htmlspecialchars($rtour['duration']); ?> hours</div>
                        <div class="inner-icon"><i class="fa-solid fa-car-side"></i> Transport Facility</div>
                        <div class="inner-icon"><i class="fa-solid fa-people-group"></i> Family Plan</div>
                        <div class="inner-wrap2">
                            <div class="star">
                                <?php
                                $rating = round($rtour['rating']);
                                for ($i = 1; $i <= 5; $i++):
                                    $star_class = ($i <= $rating) ? 'fa-solid' : 'fa-regular';
                                ?>
                                    <i class="<?php echo $star_class; ?> fa-star s<?php echo $i; ?>"></i>
                                <?php endfor; ?>
                                <p class="inner-review"><?php echo htmlspecialchars($rtour['review_count']); ?> reviews</p>
                            </div>
                            <div class="price">
                                <div class="inner-price">$<?php echo number_format($rtour['price'], 2); ?></div>
                                <p class="inner-person">per person</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No related tours available in Dubai.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- End Section 16 -->

    <!-- Section 17 -->
    <div class="section-17" data-aos="fade-up" data-aos-duration="800">
        <div class="container">
            <h2 class="inner-title">Customer Review</h2>
            <div class="box-review-list">
                <div class="box-review-left">
                    <div class="inner-score-count">
                        <div class="inner-score"><?php echo number_format($tour['rating'] ?? 4.3, 1); ?></div>
                        <div class="inner-review-count"><?php echo $tour['review_count'] ?? '854'; ?> reviews</div>
                    </div>
                    <div class="inner-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                </div>
                <div class="box-review-right">
                    <div class="rating">
                        <div class="rating-item">
                            <div class="rating-item-label">Guide</div>
                            <div class="rating-box">
                                <div class="rating-bar" style="width: 96%"></div>
                            </div>
                            <div class="rating-value">4.8</div>
                        </div>
                        <div class="rating-item">
                            <div class="rating-item-label">Transportation</div>
                            <div class="rating-box">
                                <div class="rating-bar" style="width: 60%"></div>
                            </div>
                            <div class="rating-value">3.0</div>
                        </div>
                        <div class="rating-item">
                            <div class="rating-item-label">Value for money</div>
                            <div class="rating-box">
                                <div class="rating-bar" style="width: 90%"></div>
                            </div>
                            <div class="rating-value">4.5</div>
                        </div>
                        <div class="rating-item">
                            <div class="rating-item-label">Safety</div>
                            <div class="rating-box">
                                <div class="rating-bar" style="width: 98%"></div>
                            </div>
                            <div class="rating-value">4.9</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 17 -->

    <!-- Footer -->
    <div class="Section-12">
        <div class="container">
            <div class="inner-content">
                <div class="inner-column-1">
                    <h4>Language</h4>
                    <select class="language">
                        <option>English (UK)</option>
                        <option>English (US)</option>
                        <option>Vietnamese</option>
                    </select>
                    <h4>Currency</h4>
                    <select class="money">
                        <option>U.S. Dollar ($)</option>
                        <option>Euro (€)</option>
                        <option>Japanese Yen (¥)</option>
                    </select>
                </div>
                <div class="inner-column-2">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Press Room</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="inner-column-3">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="#">Contact us</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Terms and conditions</a></li>
                        <li><a href="#">Privacy policy</a></li>
                        <li><a href="#">Sitemap</a></li>
                    </ul>
                </div>
                <div class="inner-column-4">
                    <h4>Payment methods possible</h4>
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
                    <h4>Company</h4>
                    <p>Become a Tour guide for Us</p>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer-bottom">
        <p>Copyright 2021 Tour Guide. All Rights Reserved</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-pinterest"></i></a>
        </div>
    </footer>
    <!-- End Footer -->

    <!-- Box Contact -->
    <nav class="box-contact">
        <ul>
            <li>
                <a href="#" target="_blank">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
            </li>
            <li>
                <a href="#" target="_blank">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </li>
            <li>
                <a href="#" target="_blank">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </li>
            <li>
                <a href="#" target="_blank">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </li>
        </ul>
    </nav>
    <!-- End Box Contact -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Đếm số lượng ảnh để quyết định có bật loop hay không
            const slideCount = <?php echo count($tour_images); ?>;
            const shouldLoop = slideCount > 1 && slideCount < 6; // Chỉ bật loop nếu có từ 2 đến 5 ảnh

            // Khởi tạo Swiper cho slider chính
            var swiperMain = new Swiper('.swiper-box-images-main', {
                loop: shouldLoop,
                navigation: {
                    nextEl: '.swiper-button-next-main',
                    prevEl: '.swiper-button-prev-main',
                },
                slidesPerView: 1,
                spaceBetween: 10,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });

            // Khởi tạo Swiper cho thumbnail
            var swiperThumb = new Swiper('.swiper-box-images-thumb', {
                loop: shouldLoop,
                slidesPerView: Math.min(4, slideCount || 1),
                spaceBetween: 10,
                centeredSlides: true,
                slideToClickedSlide: true,
            });

            // Đồng bộ hóa giữa slider chính và thumbnail
            if (shouldLoop) {
                swiperMain.controller.control = swiperThumb;
                swiperThumb.controller.control = swiperMain;
            }

            // Xử lý modal login
            const loginModal = document.getElementById('loginModal');
            function showLoginModal() {
                loginModal.style.display = 'flex';
            }
            function hideLoginModal() {
                loginModal.style.display = 'none';
            }

            // Đảm bảo nút close và nhấp ngoài modal hoạt động
            document.querySelector('.modal-close').addEventListener('click', hideLoginModal);
            loginModal.addEventListener('click', function(event) {
                if (event.target === loginModal) {
                    hideLoginModal();
                }
            });

            // Kích hoạt modal khi nhấp vào Confirm Booking nếu chưa đăng nhập
            document.querySelectorAll('.booking-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    <?php if (!$is_logged_in): ?>
                        e.preventDefault();
                        showLoginModal();
                    <?php endif; ?>
                });
            });

            // Xử lý modal success
            const successModal = document.getElementById('successModal');
            const closeModalBtn = document.getElementById('closeModalBtn');

            <?php if (isset($_GET['message']) && $_GET['message'] === 'Booking successful!'): ?>
                successModal.style.display = 'flex';
            <?php endif; ?>

            closeModalBtn.addEventListener('click', function() {
                successModal.style.display = 'none';
            });

            successModal.addEventListener('click', function(event) {
                if (event.target === successModal) {
                    successModal.style.display = 'none';
                }
            });
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/script-chi-tiet-tour.js"></script>
</body>

</html>