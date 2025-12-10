<?php
session_start();
include '../../db/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$status) {
        echo "❌ Invalid request.";
        exit();
    }

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "✅ User status updated to: $status";
    } else {
        echo "❌ Failed to update status.";
    }

    $stmt->close();
    $conn->close();
}
?>
