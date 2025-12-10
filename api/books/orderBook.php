<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $book_id = intval($_POST['book_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 10;

    if ($book_id <= 0 || $quantity <= 0) {
        echo "Invalid data";
        exit();
    }

    $stmt = $conn->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $book_id);

    if ($stmt->execute()) {
        echo "Ordered {$quantity} more books!";
    } else {
        echo "Error ordering books";
    }

    $stmt->close();
    $conn->close();
}
?>
