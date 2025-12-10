<?php
session_start();
include '../../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $access = $_POST['access'] ?? null;

    if (!$id || !$access) {
        echo "❌ Invalid request.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET access = ? WHERE id = ?");
    $stmt->bind_param("si", $access, $id);

    if ($stmt->execute()) {
        echo "✅ User access updated to: $access";
    } else {
        echo "❌ Failed to update access.";
    }

    $stmt->close();
    $conn->close();
}
?>
