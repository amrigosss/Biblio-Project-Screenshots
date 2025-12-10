<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo "Unauthorized";
        exit();
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;
    $role = $_POST['role'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$name || !$email || !$role) {
        echo "error: missing required fields";
        exit();
    }

    // Build dynamic query
    $query = "UPDATE users SET name=?, email=?, role=?";
    $params = [$name, $email, $role];
    $types = "sss";

    if ($password) {
        $query .= ", password=?";
        $params[] = $password;
        $types .= "s";
    }

    if ($status) {
        $query .= ", status=?";
        $params[] = $status;
        $types .= "s";
    }

    $query .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
