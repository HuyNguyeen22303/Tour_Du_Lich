<?php
session_start(); // Thêm session_start() ở đầu tệp
include 'back_end/db.php';

// Lấy danh sách tour từ cơ sở dữ liệu
try {
  $sql = "SELECT tour_id, title, duration, price, review_count, rating FROM tours LIMIT 8"; // Lấy 8 tour đầu tiên
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Lỗi truy vấn: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Thêm CSS cho thông báo */
    .notification {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      display: none;
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header class="header" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
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
          <!-- Thêm quản lý đăng nhập/đăng xuất -->
          <?php
          $is_logged_in = isset($_SESSION['user_id']);
          if ($is_logged_in) {
            echo '<button class="inner-button"><a href="back_end/logout.php">Sign out</a></button>';
          } else {
            echo '<button class="inner-button"><a href="account.php">Sign in</a></button>';
          }
          ?>
        </div>
      </div>
    </div>
  </header>

  <!-- Hộp thông báo -->
  <div id="notification" class="notification"></div>

  <?php
  if (isset($_SESSION['message'])) {
    echo '<script>document.getElementById("notification").innerText = "' . addslashes($_SESSION['message']) . '";';
    echo 'document.getElementById("notification").style.display = "block";';
    echo 'setTimeout(() => { document.getElementById("notification").style.display = "none"; }, 3000);';
    echo 'setTimeout(() => { window.location.href = "index.php"; }, 3100);';
    unset($_SESSION['message']);
    echo '</script>';
  }
  ?>

  <!-- Section 1 -->
  <div class="section-1">
    <div class="container">
      <div class="inne-wrap">
        <h1 class="inner-title" data-aos="fade-up" data-aos-duration="800">We Find The Best Tours For You</h1>
        <p class="inner-desc" data-aos-duration="800" data-aos-delay="150">
          Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint.
          <br />
          Velit officia consequat duis enim velit mollit. Exercitation veniam consequat
          <br />
          sunt nostrud amet.
        </p>
        <div class="inner-icon">
          <img src="assets/images/icon-section-1.svg" alt="" class="">
          <span class="">Watch Video</span>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section 1 -->

  <!-- Section 2 -->
  <div class="section-2" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-wrap">
        <div class="search-bar" data-aos="fade-up" data-aos-duration="800">
          <div class="search-item">
            <div class="search-content">
              <i class="fa-solid fa-location-dot"></i> Location
            </div>
            <div class="search-desc">Search For A Destination</div>
          </div>
          <div class="search-item">
            <div class="search-content">
              <i class="fa-solid fa-user-group"></i> Guests
            </div>
            <div class="search-desc">How many Guests?</div>
          </div>
          <div class="search-item">
            <div class="search-content">
              <i class="fa-solid fa-calendar-alt"></i> Date
            </div>
            <div class="search-desc">Pick a date</div>
          </div>
        </div>
        <button class="search-button">Search</button>
      </div>
    </div>
  </div>
  <!-- End Section 2 -->

  <!-- Section-3 -->
  <div class="section-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-Group-221">
        <h2 class="inner-title">Explore Popular Cities</h2>
        <p class="inner-nd">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit
          officia <br>consequat duis enim velit mollit</p>
      </div>
    </div>
  </div>
  <!-- End Section-3 -->

  <!-- Section-4 -->
  <div class="section-4" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-wrap">
        <div class="box" data-country="new-york">
          <p>New York</p>
        </div>
        <div class="box" data-country="california">
          <p>California</p>
        </div>
        <div class="box active" data-country="alaska">
          <p>Alaska</p>
        </div>
        <div class="box" data-country="sydney">
          <p>Sydney</p>
        </div>
        <div class="box" data-country="dubai">
          <p>Dubai</p>
        </div>
        <div class="box" data-country="london">
          <p>London</p>
        </div>
        <div class="box" data-country="tokyo">
          <p>Tokyo</p>
        </div>
        <div class="box" data-country="delhi">
          <p>Delhi</p>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section-4 -->

  <!-- Section-5 -->
  <div class="section-5" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-image">
        <img class="img" src="assets/images/alaska.jpg" alt="">
      </div>
      <div class="inner-wrap">
        <div class="inner-content">
          <div class="inner-infor-1">
            <h2 class="title">Alaska</h2>
            <p class="inner-nd">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia
              consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.</p>
          </div>

          <div class="inner-infor-2">
            <div class="box box-1"><i class="fa-solid fa-bus-simple"></i>Public Transportations</div>
            <div class="box box-2"><i class="fa-solid fa-earth-asia"></i>Nature & Adventure</div>
            <div class="box box-3"><i class="fa-solid fa-taxi"></i>Private Transportations</div>
            <div class="box box-4"><i class="fa-solid fa-briefcase"></i>Business Tours</div>
            <div class="box box-5"><i class="fa-solid fa-location-dot"></i>Local Visit</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section-5 -->

  <div class="section-6">
    <div class="container">
      <div class="swiper swiper-section-6" data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">
        <div class="swiper-wrapper" id="swiper-wrapper">

          <script>
            fetch('back_end/get_tours.php')
              .then(response => response.json())
              .then(data => {
                const wrapper = document.getElementById('swiper-wrapper');
                // Đối tượng để theo dõi các tour đã thêm
                const addedTours = new Set();

                data.forEach(tour => {
                  // Chỉ thêm tour nếu chưa tồn tại
                  if (!addedTours.has(tour.tour_id)) {
                    const slide = document.createElement('div');
                    slide.classList.add('swiper-slide');
                    slide.innerHTML = `
            <a href="./chi-tiet-tour.php?id=${tour.tour_id}">
              <div class="box">
                <img class="inner-image" src="${tour.image_path}" alt="">
                <h3 class="inner-title">${tour.title}</h3>
                <div class="inner-icon"><i class="fa-solid fa-clock"></i> Duration ${tour.duration || '3 days'}</div>
                <div class="inner-icon"><i class="fa-solid fa-car-side"></i> Transport Facility</div>
                <div class="inner-icon"><i class="fa-solid fa-people-group"></i> Family Plan</div>
                <hr>
                <div class="inner-wrap2">
                  <div class="star">
                    <i class="fa-solid fa-star s1"></i>
                    <i class="fa-solid fa-star s2"></i>
                    <i class="fa-solid fa-star s3"></i>
                    <i class="fa-solid fa-star s4"></i>
                    <i class="fa-solid fa-star s5"></i>
                    <p class="inner-review">${tour.review_count || 584} reviews</p>
                  </div>
                  <div class="price">
                    <div class="inner-price">${Number(tour.price).toLocaleString()} $</div>
                    <p class="inner-person">per person</p>
                  </div>
                </div>
              </div>
            </a>
          `;
                    wrapper.appendChild(slide);
                    addedTours.add(tour.tour_id); // Đánh dấu tour đã thêm
                  }
                });

                if (window.swiperSection6) {
                  window.swiperSection6.update();
                }
              })
              .catch(err => console.error('Lỗi lấy dữ liệu tour:', err));
          </script>


        </div>
      </div>
    </div>
  </div>


  <!-- End Section 6 -->

  <!-- Section 7 -->
  <div class="section-7" data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">
    <div class="container">
      <div class="inner-wrap">
        <img src="assets/images/Section-7-item-1.png" alt="image" class="img-1">
        <img src="assets/images/Section-7-item-2.png" alt="image" class="img-2">
        <img src="assets/images/Section-7-item-3.png" alt="image" class="img-3">
      </div>
      <div class="inner-content">
        <div class="button-1"><a href="">TRENDING NOW</a></div>
        <h2 class="inner-title">Wilderlife Of Alaska</h2>
        <div class="iner-local-star">
          <span class="local">
            <a href=""><i class="fa-solid fa-location-dot"></i> Alaska, USA</a>
          </span>
          <span> | </span>
          <span class="star">
            <a href="">
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              <i class="fa-solid fa-star"></i>
              4.9 ( 300 reviews )
            </a>
          </span>
        </div>
        <p class="inner-desc">
          Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint...
        </p>
        <div class="button">
          <div class="button-2"><a href="chi-tiet-tour.php">Book Now</a></div>
          <div class="icon-box">
            <div class="icon icon-1">
              <a href=""><i class="fa-solid fa-heart"></i></a>
            </div>
            <div class="icon">
              <a href=""><i class="fa-solid fa-share"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section 7 -->

  <!-- Section 8 -->
  <div class="section-8">
    <div class="container">
      <div class="inner-title">
        <h2 class="inner-content" data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">Featured Destinations
        </h2>
        <p class="inner-desc" data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">Amet minim mollit non
          deserunt ullamco est sit aliqua dolor do amet<br /> sint. Velit officia consequat duis enim velit mollit</p>
      </div>

      <div class="box-icon">
        <div class="icon">
          <a class="lessthan" href="#"><i class="fa-regular fa-less-than"></i></a>
        </div>
        <div class="icon">
          <a class="greaterthan" href="#"><i class="fa-regular fa-greater-than"></i></a>
        </div>
      </div>
    </div>
    <div class="inner-trip">
      <?php foreach ($tours as $tour): ?>
        <div class="inner-product" onclick="redirectToTour(<?php echo $tour['tour_id']; ?>)">
          <div class="box">
            <a href="chi-tiet-tour.php?id=<?php echo $tour['tour_id']; ?>">
              <?php
              $tour_id = $tour['tour_id'];
              $sql_image = "SELECT image_path FROM tour_images WHERE tour_id = :tour_id LIMIT 1";
              $stmt_image = $conn->prepare($sql_image);
              $stmt_image->execute(['tour_id' => $tour_id]);
              $image = $stmt_image->fetch(PDO::FETCH_ASSOC);
              $image_path = $image ? htmlspecialchars($image['image_path']) : 'assets/images/default-tour.jpg';
              ?>
              <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>">
            </a>
            <h4 class="trip-title"><?php echo htmlspecialchars($tour['title']); ?></h4>
            <p><i class="fa-solid fa-clock"></i> Durations <?php echo htmlspecialchars($tour['duration']); ?></p>
            <p><i class="fa-solid fa-car-side"></i> Transport facility</p>
            <p><i class="fa-solid fa-people-group"></i> Family Plan</p>
          </div>
          <div class="inner-price">
            <div class="inner-rating">
              <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="fa-solid fa-star <?php echo $i <= $tour['rating'] ? '' : 'fa-regular'; ?>"></i>
                <?php endfor; ?>
              </div>
              <p><?php echo $tour['review_count'] ?? 584; ?> reviews</p>
            </div>
            <div class="price">
              <h4>$<?php echo number_format($tour['price'], ); ?></h4>
              <p>per person</p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <!-- End Section 8 -->

  <!-- Section 9 -->
  <div class="section-9" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-wrap">
        <img src="assets/images/Section-9-item-1.png" alt="" class="hotel">
        <img src="assets/images/Section-9-item-2.png" alt="" class="map">
      </div>
      <div class="inner-content">
        <h2 class="inner-title">Smart City Tour Mobile App</h2>
        <h4 class="sub-title">Available on IOS & Android</h4>
        <p class="inner-desc">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia
          consequat duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.</p>
        <div class="buttons">
          <button class="ios">
            <a href="#" class=""><i class="fa-brands fa-apple"></i> Download For IOS</a>
          </button>
          <button class="android">
            <a href="#" class=""><i class="fa-brands fa-android"></i> Download For Android</a>
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section 9 -->

  <!-- Section 10 -->
  <div class="section-10" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-wrap">
        <div class="inner-tq">
          <div class="inner-title">
            <h3 class="title">From The Gallery</h3>
            <p class="inner-text">Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint.<br> Velit
              officia consequat duis enim velit mollit</p>
          </div>
          <a href="#" class="inner-button" id="viewAllBtn">View All Images</a>
          <a href="#" class="inner-button" id="hideAllBtn" style="display: none;">Hidden Images</a>
        </div>
        <div class="inner-list" id="imageList">
          <div class="inner-item">
            <img src="assets/images/Section-10-item-1.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-2.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-3.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-4.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-5.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-6.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-7.jpg" alt="" class="">
          </div>
          <div class="inner-item">
            <img src="assets/images/Section-10-item-8.jpg" alt="" class="">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Section 10 -->

  <!-- Section 11 -->
  <div class="section-11" data-aos="fade-up" data-aos-duration="800" data-aos-delay="450">
    <div class="container">
      <div class="inner-wrap">
        <div class="inner-tq">
          <div class="inner-title">
            <h3 class="title">Latest Stories</h3>
            <p class="inner-text">
              Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint.
              <br> Velit officia consequat duis enim velit mollit
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
  <!-- End Section 11 -->

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

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>