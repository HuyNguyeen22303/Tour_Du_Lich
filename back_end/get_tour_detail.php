<?php
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

header('Content-Type: application/json');

if (!$id) {
    echo json_encode(['error' => 'Thiếu ID']);
    exit;
}

try {
    // Lấy tour
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$id]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tour) {
        // Ghép đường dẫn ảnh chính
        $tour['image_url'] = 'assets/' . $tour['image_url'];

        // Lấy ảnh phụ
        $imgStmt = $conn->prepare("SELECT image_url FROM tour_images WHERE tour_id = ?");
        $imgStmt->execute([$id]);
        $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

        // Ghép đường dẫn ảnh phụ
        $tour['extra_images'] = array_map(function($img) {
            return str_starts_with($img, 'assets/') ? $img : 'assets/' . $img;
        }, $images);


        echo json_encode($tour);
    } else {
        echo json_encode(['error' => 'Tour không tồn tại']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Lỗi truy vấn: ' . $e->getMessage()]);
}
?>
