<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $supplier_id = (int) $_POST['supplier'];
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
    $description = trim($_POST['description']);

    $cover_image = null;

    // ✅ Check if file was uploaded
    if (!empty($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $target_dir = "../../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // create uploads dir if not exists
        }

        $cover_image = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $cover_image;

        if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            echo "Error uploading image.";
            exit();
        }
    } else {
        $cover_image = "hobit_cover.png"; // default fallback image
    }

    // ✅ Include stock and description in the insert query
    $stmt = $conn->prepare("INSERT INTO books (title, author, genre, stock, supplier_id, cover_image, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $title, $author, $genre, $stock, $supplier_id, $cover_image, $description);

    echo $stmt->execute() ? "success" : "Error: " . $stmt->error;

    $stmt->close();
    $conn->close();
}
?>
