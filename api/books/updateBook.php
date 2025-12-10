<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookId = $_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = $_POST['genre'];
    $supplier = $_POST['supplier'];
    $stock = $_POST['stock'];
    $description = trim($_POST['description']);

    if (!$bookId || !$title || !$author || !$genre || !$supplier || $stock === '') {
        echo "error: missing fields";
        exit();
    }

    $cover_image = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $cover_image = $_FILES['cover_image']['name'];
        $target_dir = "../../uploads/";
        $target_file = $target_dir . basename($cover_image);
        move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file);
    }

    if ($cover_image) {
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, genre=?, supplier_id=?, stock=?, cover_image=?, description=? WHERE id=?");
        $stmt->bind_param("sssisssi", $title, $author, $genre, $supplier, $stock, $cover_image, $description, $bookId);
    } else {
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, genre=?, supplier_id=?, stock=?, description=? WHERE id=?");
        $stmt->bind_param("sssissi", $title, $author, $genre, $supplier, $stock, $description, $bookId);
    }

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
