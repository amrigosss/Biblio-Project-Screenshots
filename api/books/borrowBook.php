<?php
session_start();
include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id']) || !isset($data['book_id'])) {
    echo "error";
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = $data['book_id'];

// Check if user already has an active borrow request for this book
$checkDuplicate = $conn->prepare("
    SELECT COUNT(*) FROM borrow_requests
    WHERE user_id = ? AND book_id = ? AND status IN ('pending', 'approved')
    AND (return_status IS NULL OR return_status != 'returned')
");
$checkDuplicate->bind_param("ii", $user_id, $book_id);
$checkDuplicate->execute();
$checkDuplicate->bind_result($count);
$checkDuplicate->fetch();
$checkDuplicate->close();

if ($count > 0) {
    echo "duplicate"; // indicate duplicate request found
    exit();
}

// Check if book has stock
$check = $conn->prepare("SELECT stock FROM books WHERE id = ?");
$check->bind_param("i", $book_id);
$check->execute();
$result = $check->get_result();
$book = $result->fetch_assoc();

if (!$book || $book['stock'] <= 0) {
    echo "error";
    exit();
}

// Insert borrow request
$stmt = $conn->prepare("INSERT INTO borrow_requests (user_id, book_id, status, request_date) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    // Decrease book stock
    $updateStock = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = ? AND stock > 0");
    $updateStock->bind_param("i", $book_id);
    $updateStock->execute();
    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
