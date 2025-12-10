<?php
include '../../db/connect.php';

$bookId = $_GET['book_id'];

$sql = "SELECT users.name, r.rating, r.review, r.created_at 
        FROM book_reviews r 
        JOIN users ON r.user_id = users.id 
        WHERE r.book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookId);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
echo json_encode($reviews);
?>
