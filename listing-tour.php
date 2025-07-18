<?php
// Kết nối cơ sở dữ liệu
try {
    $conn = new PDO("mysql:host=127.0.0.1;dbname=tour_website_new;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Khởi tạo session
session_start();
$is_logged_in = isset($_SESSION['user_id']); // Thay bằng logic kiểm tra đăng nhập thực tế

// Lấy địa điểm từ query string, mặc định là London
$location = isset($_GET['location']) ? $_GET['location'] : 'London';
$page_title = "Things to do in " . htmlspecialchars($location);

// Xử lý logout
if (isset($_GET['logout']) && $is_logged_in) {
    session_unset();
    session_destroy();
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, "/");
    }
    header("Location: listing-tour.php?message=Đăng xuất thành công");
    exit();
}

// Phân trang
$per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Đếm tổng số tour theo địa điểm
$sql_count = "SELECT COUNT(*) FROM tours WHERE location = :location";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bindParam(':location', $location, PDO::PARAM_STR);
$stmt_count->execute();
$total_tours = $stmt_count->fetchColumn();
$total_pages = ceil($total_tours / $per_page);

// Lấy danh sách tour
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'popularity';
$order_by = $sort_by == 'rating' ? "t.rating DESC" : 
            ($sort_by == 'newest' ? "t.tour_id DESC" : 
            ($sort_by == 'price_low_high' ? "t.price ASC" : 
            ($sort_by == 'price_high_low' ? "t.price DESC" : "t.review_count DESC")));

$sql = "SELECT t.tour_id, t.title, t.location, t.duration, t.price, t.rating, t.review_count, t.category, ti.image_path 
        FROM tours t 
        LEFT JOIN tour_images ti ON t.tour_id = ti.tour_id 
        WHERE t.location = :location 
        ORDER BY $order_by 
        LIMIT :offset, :per_page";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':location', $location, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();
$tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách duration
$sql_durations = "SELECT DISTINCT duration FROM tours WHERE location = :location";
$stmt_durations = $conn->prepare($sql_durations);
$stmt_durations->bindParam(':location', $location, PDO::PARAM_STR);
$stmt_durations->execute();
$durations = $stmt_durations->fetchAll(PDO::FETCH_COLUMN);

// Lấy danh sách destination
$sql_destinations = "SELECT DISTINCT location FROM tours";
$stmt_destinations = $conn->prepare($sql_destinations);
$stmt_destinations->execute();
$destinations = $stmt_destinations->fetchAll(PDO::FETCH_COLUMN);

// Lấy danh sách tour theo category cho Section-20
$categories = ['Water Activities', 'Special Foods', 'River Activity'];
$category_tours = [];
foreach ($categories as $category) {
    $sql_category = "SELECT t.tour_id, t.title, t.duration, t.price, t.rating, t.review_count, ti.image_path 
                     FROM tours t 
                     LEFT JOIN tour_images ti ON t.tour_id = ti.tour_id 
                     WHERE t.category = :category 
                     LIMIT 8";
    $stmt_category = $conn->prepare($sql_category);
    $stmt_category->bindParam(':category', $category, PDO::PARAM_STR);
    $stmt_category->execute();
    $category_tours[$category] = $stmt_category->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
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
        /* Pagination Styles */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }
        .pagination a:hover {
            background-color: #0056b3;
            color: #fff;
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
                            <li><a href="listing-tour.php">Popular Destinations</a></li>
                            <li><a href="#">Our Packages</a></li>
                            <li><a href="#">Help</a></li>
                        </ul>
                        <div class="inner-overlay"></div>
                    </nav>
                    <?php if ($is_logged_in): ?>
                        <button class="inner-button" onclick="window.location.href='listing-tour.php?logout=true'">Sign Out</button>
                    <?php else: ?>
                        <button class="inner-button" onclick="showLoginModal()">Sign In</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <!-- End Header -->

    <!-- Section 18 -->
    <div class="section-18">
        <div class="container">
            <div class="head-title">
                <h2 class="inner-title"><?php echo htmlspecialchars($page_title); ?></h2>
                <p class="inner-desc"><?php echo $total_tours; ?> activities found</p>
            </div>
            <div class="drop-down">
                <label>Sort by:</label>
                <select onchange="window.location.href='listing-tour.php?location=<?php echo urlencode($location); ?>&sort=' + this.value">
                    <option value="popularity" <?php echo $sort_by == 'popularity' ? 'selected' : ''; ?>>Popularity</option>
                    <option value="rating" <?php echo $sort_by == 'rating' ? 'selected' : ''; ?>>Top rated</option>
                    <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price_low_high" <?php echo $sort_by == 'price_low_high' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high_low" <?php echo $sort_by == 'price_high_low' ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>
    <!-- End Section 18 -->

    <!-- Section 19 -->
    <div class="section-19">
        <div class="container">
            <div class="inner-wrap">
                <div class="inner-left">
                    <div class="box-item box-1">
                        <div class="inner-title">Availability</div>
                        <form action="listing-tour.php?location=<?php echo urlencode($location); ?>" method="POST" class="inner-form" onsubmit="return checkLogin(event)">
                            <div class="inner-group">
                                <label for="from_date" class="inner-label">From</label>
                                <input type="date" name="from_date" id="from_date" class="inner-group" value="<?php echo isset($_POST['from_date']) ? htmlspecialchars($_POST['from_date']) : ''; ?>">
                            </div>
                            <div class="inner-group">
                                <label for="to_date" class="inner-label">To</label>
                                <input type="date" name="to_date" id="to_date" class="inner-group" value="<?php echo isset($_POST['to_date']) ? htmlspecialchars($_POST['to_date']) : ''; ?>">
                            </div>
                            <button type="submit">Check Availability</button>
                        </form>
                    </div>
                    <!-- Các box-item khác giữ nguyên -->
                    <div class="box-item box-2">
                        <div class="head-title">
                            <div class="inner-title">Activities</div>
                            <span><i class="fa-solid fa-sort-down"></i></span>
                        </div>
                        <form action="" class="inner-form">
                            <div class="inner-group">
                                <input type="checkbox" name="filter_water" value="Water Activities">
                                <label>Water activities</label><br>
                                <input type="checkbox" name="filter_social" value="Good for social distancing">
                                <label>Good for social distancing</label><br>
                                <input type="checkbox" name="filter_adrenaline" value="Adrenaline">
                                <label>Adrenaline</label><br>
                                <input type="checkbox" name="filter_nature" value="Nature">
                                <label>Nature</label><br>
                                <input type="checkbox" name="filter_hidden" value="Hidden gems">
                                <label>Hidden gems</label><br>
                                <input type="checkbox" name="filter_street" value="Street art & graffiti">
                                <label>Street art & graffiti</label><br>
                                <input type="checkbox" name="filter_food" value="Food">
                                <label>Food</label>
                            </div>
                        </form>
                        <span class="show-more-btn">Show More Destinations</span>
                    </div>
                    <div class="box-item box-2">
                        <div class="head-title">
                            <div class="inner-title">Duration</div>
                            <span><i class="fa-solid fa-sort-down"></i></span>
                        </div>
                        <form action="" class="inner-form">
                            <div class="inner-group">
                                <?php foreach ($durations as $duration): ?>
                                    <input type="checkbox" name="duration_<?php echo htmlspecialchars($duration); ?>" value="<?php echo htmlspecialchars($duration); ?>">
                                    <label><?php echo htmlspecialchars($duration); ?></label><br>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </div>
                    <div class="box-item box-2">
                        <div class="head-title">
                            <div class="inner-title">Destination</div>
                            <span><i class="fa-solid fa-sort-down"></i></span>
                        </div>
                        <form action="" class="inner-form">
                            <div class="inner-group">
                                <?php foreach ($destinations as $dest): ?>
                                    <input type="checkbox" name="dest_<?php echo htmlspecialchars($dest); ?>" value="<?php echo htmlspecialchars($dest); ?>">
                                    <label><?php echo htmlspecialchars($dest); ?></label><br>
                                <?php endforeach; ?>
                            </div>
                        </form>
                        <span class="show-more-btn">Show More Destinations</span>
                    </div>
                </div>
                <div class="inner-right">
                    <?php if (!empty($tours)): ?>
                        <?php foreach ($tours as $tour): ?>
                            <div class="box-tour-item tour-card">
                                <a href="chi-tiet-tour.php?id=<?php echo $tour['tour_id']; ?>">
                                    <img src="<?php echo htmlspecialchars($tour['image_path'] ?: 'assets/images/default-tour.jpg'); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                </a>
                                <div class="tour-content">
                                    <div class="top-head">
                                        <span class="badge-green"><?php echo htmlspecialchars($tour['category'] ?: 'Water Activities'); ?></span>
                                        <span>|</span>
                                        <div class="rating">
                                            <div class="stars">
                                                <?php
                                                $rating = round($tour['rating']);
                                                for ($i = 1; $i <= 5; $i++):
                                                    $star_class = ($i <= $rating) ? 'fa-solid' : 'fa-regular';
                                                ?>
                                                    <i class="<?php echo $star_class; ?> fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="review">(<?php echo htmlspecialchars($tour['review_count']); ?> reviews)</span>
                                        </div>
                                    </div>
                                    <div class="tour-text">
                                        <h2 class="tour-title"><?php echo htmlspecialchars($tour['title']); ?></h2>
                                        <div class="tour-price">
                                            <div class="price">$<?php echo number_format($tour['price'], 2); ?></div>
                                            <div class="text">per person</div>
                                        </div>
                                    </div>
                                    <div class="tour-info">
                                        <div class="inner-item"><i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?></div>
                                        <span>|</span>
                                        <div class="inner-item"><i class="fa-solid fa-car"></i> Transport</div>
                                        <span>|</span>
                                        <div class="inner-item"><i class="fa-solid fa-user-group"></i> Family Plan</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- Phân trang -->
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="listing-tour.php?location=<?php echo urlencode($location); ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page - 1; ?>">« Previous</a>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="listing-tour.php?location=<?php echo urlencode($location); ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <a href="listing-tour.php?location=<?php echo urlencode($location); ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $page + 1; ?>">Next »</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p>No tours available in <?php echo htmlspecialchars($location); ?>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 19 -->

    <!-- Section-20 -->
    <div class="section-20">
        <div class="container">
            <div class="inner-wrap">
                <h2>Outside the City Specials</h2>
                <?php foreach ($categories as $category): ?>
                    <div class="box-item-<?php echo array_search($category, $categories) + 1; ?>" data-category="<?php echo htmlspecialchars($category); ?>">
                        <div class="box-icon-action">
                            <div class="badge-<?php echo $category == 'Water Activities' ? 'green' : ($category == 'Special Foods' ? 'blue' : 'red'); ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </div>
                            <div class="box-icon">
                                <div class="icon">
                                    <a class="lessthan-20" href="javascript:void(0)" onclick="prevTours('<?php echo htmlspecialchars($category); ?>')"><i class="fa-regular fa-less-than"></i></a>
                                </div>
                                <div class="icon">
                                    <a class="greaterthan-20" href="javascript:void(0)" onclick="nextTours('<?php echo htmlspecialchars($category); ?>')"><i class="fa-regular fa-greater-than"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="inner-trip" id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>-tours">
                            <?php 
                            $tours = $category_tours[$category];
                            $image_counter = 1;
                            foreach ($tours as $index => $tour): 
                            ?>
                                <div class="box box-<?php echo $image_counter; ?>" data-index="<?php echo $index; ?>" style="display: <?php echo $index < 4 ? 'block' : 'none'; ?>;">
                                    <a href="chi-tiet-tour.php?id=<?php echo $tour['tour_id']; ?>">
                                        <img class="inner-image" src="<?php echo htmlspecialchars($tour['image_path'] ?: 'assets/images/default-tour.jpg'); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                    </a>
                                    <h3 class="inner-title"><?php echo htmlspecialchars($tour['title']); ?></h3>
                                    <div class="inner-icon"><i class="fa-solid fa-clock"></i> Duration <?php echo htmlspecialchars($tour['duration']); ?></div>
                                    <div class="inner-icon"><i class="fa-solid fa-car-side"></i> Transport Facility</div>
                                    <div class="inner-icon"><i class="fa-solid fa-people-group"></i> Family Plan</div>
                                    <div class="inner-wrap2">
                                        <div class="star">
                                            <?php
                                            $rating = round($tour['rating']);
                                            for ($i = 1; $i <= 5; $i++):
                                                $star_class = ($i <= $rating) ? 'fa-solid' : 'fa-regular';
                                            ?>
                                                <i class="<?php echo $star_class; ?> fa-star s<?php echo $i; ?>"></i>
                                            <?php endfor; ?>
                                            <p class="inner-review"><?php echo htmlspecialchars($tour['review_count']); ?> reviews</p>
                                        </div>
                                        <div class="price">
                                            <div class="inner-price">$<?php echo number_format($tour['price'], 2); ?></div>
                                            <p class="inner-person">per person</p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                $image_counter++;
                                if ($image_counter > 4) $image_counter = 1;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- End Section 20 -->

    <!-- Section 21 -->
    <div class="section-21">
        <div class="container">
            <div class="inner-wrap">
                <div class="inner-tq">
                    <div class="inner-title">
                        <h3 class="title">From The Gallery</h3>
                        <p class="inner-text">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint.<br> Velit officia consequat duis enim velit mollit</p>
                    </div>
                    <a href="#" class="inner-button" id="viewAllBtn">View All Images</a>
                    <a href="#" class="inner-button" id="hideAllBtn" style="display: none;">Hidden Images</a>
                </div>
                <div class="inner-list" id="imageList">
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-1.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-2.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-3.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-4.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-5.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-6.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-7.jpg" alt="">
                    </div>
                    <div class="inner-item">
                        <img src="assets/images/Section-10-item-8.jpg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 21 -->

    <!-- Section 22 -->
    <div class="section-22">
        <div class="container">
            <div class="inner-wrap">
                <div class="inner-tq">
                    <div class="inner-title">
                        <h3 class="title">Latest Stories</h3>
                        <p class="inner-text">
                            Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint.
                            <br> Vel  officia consequat duis enim velit mollit
                        </p>
                    </div>
                    <a href="#" class="inner-button" id="viewAllPost">View All Posts</a>
                    <a href="#" class="inner-button" id="hidePost" style="display: none;">Hidden Posts</a>
                </div>
                <div class="inner-list" id="postList">
                    <div class="inner-item hidden">
                        <div class="inner-image">
                            <img src="assets/images/Section-11-item-1.jpg" alt="">
                        </div>
                        <div class="inner-info">
                            <div class="inner-img">
                                <img src="assets/images/Section-11.jpg" alt="">
                            </div>
                            <div class="inner-name">Jackie Moncada</div>
                        </div>
                        <div class="inner-text">7 Signs and Symptoms of <br> Iodine Deficiency</div>
                    </div>
                    <div class="inner-item hidden">
                        <div class="inner-image">
                            <img src="assets/images/Section-11-item-2.jpg" alt="">
                        </div>
                        <div class="inner-info">
                            <div class="inner-img">
                                <img src="assets/images/Section-11.jpg" alt="">
                            </div>
                            <div class="inner-name">Jackie Moncada</div>
                        </div>
                        <div class="inner-text">7 Signs and Symptoms of <br> Iodine Deficiency</div>
                    </div>
                    <div class="inner-item hidden">
                        <div class="inner-image">
                            <img src="assets/images/Section-11-item-3.jpg" alt="">
                        </div>
                        <div class="inner-info">
                            <div class="inner-img">
                                <img src="assets/images/Section-11.jpg" alt="">
                            </div>
                            <div class="inner-name">Jackie Moncada</div>
                        </div>
                        <div class="inner-text">7 Signs and Symptoms of <br> Iodine Deficiency</div>
                    </div>
                    <div class="inner-item hidden">
                        <div class="inner-image">
                            <img src="assets/images/Section-11-item-4.jpg" alt="">
                        </div>
                        <div class="inner-info">
                            <div class="inner-img">
                                <img src="assets/images/Section-11.jpg" alt="">
                            </div>
                            <div class="inner-name">Jackie Moncada</div>
                        </div>
                        <div class="inner-text">7 Signs and Symptoms of <br> Iodine Deficiency</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Section 22 -->

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
            <li><a href="#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-whatsapp"></i></a></li>
            <li><a href="#" target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
        </ul>
    </nav>
    <!-- End Box Contact -->

    <!-- Modal cho đăng nhập -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="hideLoginModal()">×</button>
            <h2>Sign In</h2>
            <p>Please log in to continue.</p>
            <button class="modal-button" onclick="window.location.href='account.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'">Sign In</button>
        </div>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message" style="text-align: center; color: green; margin: 15px 0;">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['message'])): ?>
        <div class="message" style="text-align: center; color: green; margin: 15px 0;">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="error" style="text-align: center; color: red; margin: 15px 0;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <script>
        // Xử lý modal login
        function showLoginModal() {
            const loginModal = document.getElementById('loginModal');
            loginModal.style.display = 'flex';
        }

        function hideLoginModal() {
            const loginModal = document.getElementById('loginModal');
            loginModal.style.display = 'none';
        }

        // Kiểm tra đăng nhập trước khi submit form
        function checkLogin(event) {
            <?php if (!$is_logged_in): ?>
                event.preventDefault();
                showLoginModal();
                return false;
            <?php endif; ?>
            return true;
        }

        // Xử lý toggle hình ảnh
        document.getElementById('viewAllBtn').addEventListener('click', function() {
            const imageList = document.getElementById('imageList');
            imageList.style.display = 'flex';
            this.style.display = 'none';
            document.getElementById('hideAllBtn').style.display = 'inline-block';
        });

        document.getElementById('hideAllBtn').addEventListener('click', function() {
            const imageList = document.getElementById('imageList');
            imageList.style.display = 'none';
            this.style.display = 'none';
            document.getElementById('viewAllBtn').style.display = 'inline-block';
        });

        // Xử lý toggle bài viết
        document.getElementById('viewAllPost').addEventListener('click', function() {
            const postList = document.getElementById('postList');
            const items = postList.getElementsByClassName('inner-item');
            for (let item of items) {
                item.classList.remove('hidden');
            }
            this.style.display = 'none';
            document.getElementById('hidePost').style.display = 'inline-block';
        });

        document.getElementById('hidePost').addEventListener('click', function() {
            const postList = document.getElementById('postList');
            const items = postList.getElementsByClassName('inner-item');
            for (let item of items) {
                item.classList.add('hidden');
            }
            this.style.display = 'none';
            document.getElementById('viewAllPost').style.display = 'inline-block';
        });

        // Đóng modal khi nhấp ra ngoài
        document.getElementById('loginModal').addEventListener('click', function(event) {
            if (event.target === this) {
                hideLoginModal();
            }
        });

        // Xử lý carousel cho Section-20
        function prevTours(category) {
            const container = document.getElementById(category.toLowerCase().replace(' ', '-') + '-tours');
            const items = container.getElementsByClassName('box');
            let currentIndex = -1;
            for (let i = 0; i < items.length; i++) {
                if (items[i].style.display === 'block') {
                    currentIndex = parseInt(items[i].getAttribute('data-index'));
                    break;
                }
            }
            if (currentIndex > 0) {
                for (let i = 0; i < items.length; i++) {
                    items[i].style.display = (parseInt(items[i].getAttribute('data-index')) >= currentIndex - 4 && parseInt(items[i].getAttribute('data-index')) < currentIndex) ? 'block' : 'none';
                }
            }
        }

        function nextTours(category) {
            const container = document.getElementById(category.toLowerCase().replace(' ', '-') + '-tours');
            const items = container.getElementsByClassName('box');
            let currentIndex = -1;
            for (let i = 0; i < items.length; i++) {
                if (items[i].style.display === 'block') {
                    currentIndex = parseInt(items[i].getAttribute('data-index'));
                    break;
                }
            }
            if (currentIndex + 4 < items.length) {
                for (let i = 0; i < items.length; i++) {
                    items[i].style.display = (parseInt(items[i].getAttribute('data-index')) > currentIndex && parseInt(items[i].getAttribute('data-index')) <= currentIndex + 4) ? 'block' : 'none';
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>