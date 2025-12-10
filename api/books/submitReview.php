<?php
session_start();
include '../../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);

    $check = $conn->prepare("SELECT * FROM reviews WHERE user_id = ? AND book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "You already reviewed this book.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $comment);
    if ($stmt->execute()) {
        header("Location: ../../user/book_details.php?id=$book_id");
    } else {
        echo "Error submitting review.";
    }
    $stmt->close();
}
?>
