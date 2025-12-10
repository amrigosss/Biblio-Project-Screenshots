<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo "Unauthorized";
        exit();
    }

    // Support both JSON and form-data
    $data = json_decode(file_get_contents("php://input"), true);
    $id = isset($_POST['id']) ? $_POST['id'] : ($data['id'] ?? null);

    if (!$id) {
        echo "Missing ID";
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    echo $stmt->execute() ? "success" : "Error: " . $stmt->error;

    $stmt->close();
    $conn->close();
}
?>
