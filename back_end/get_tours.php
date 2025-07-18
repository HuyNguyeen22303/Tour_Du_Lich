<?php
include 'db.php';

try {
    $stmt = $conn->prepare("SELECT tour_id, title, description, price, duration, review_count, location FROM tours ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tours as &$tour) {
        // Tạo đường dẫn ảnh dựa trên location (chuyển thành tên file)
        $location = strtolower(str_replace(' ', '', $tour['location'])); // Loại bỏ khoảng trắng và chuyển thành chữ thường
        $tour['image_path'] = "assets/imagesTour/{$location}.jpg"; // Giả định tên file khớp với location

        // Đảm bảo các giá trị mặc định
        $tour['duration'] = $tour['duration'] ?: '3 days';
        $tour['review_count'] = $tour['review_count'] ?: 0;
    }

    header('Content-Type: application/json');
    echo json_encode($tours);
} catch (PDOException $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Lỗi truy vấn: ' . $e->getMessage()]);
}
?>