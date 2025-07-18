<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tour_id = isset($_POST['tour_id']) ? (int)$_POST['tour_id'] : 0;
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $guests = $_POST['guests'];

        // Lưu thông tin đặt tour vào bảng bookings
        $sql = "INSERT INTO bookings (tour_id, from_date, to_date, guests) VALUES (:tour_id, :from_date, :to_date, :guests)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':from_date', $from_date);
        $stmt->bindParam(':to_date', $to_date);
        $stmt->bindParam(':guests', $guests);
        $stmt->execute();

        // Chuyển hướng về trang chi tiết tour với thông báo thành công
        header("Location: ../chi-tiet-tour.php?id=$tour_id&message=Booking successful!");
        exit();
    } catch (PDOException $e) {
        // Chuyển hướng với thông báo lỗi
        header("Location: ../chi-tiet-tour.php?id=$tour_id&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    echo "Invalid request method.";
}
?>