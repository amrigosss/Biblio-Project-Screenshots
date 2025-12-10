<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION['user_id'];
    $bookId = $_POST['book_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO book_reviews (user_id, book_id, rating, review, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $userId, $bookId, $rating, $review);

    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Failed to submit review.";
    }
}
?>
