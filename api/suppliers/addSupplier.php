<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

include '../../db/connect.php';

$name = $_POST['name'];
$contact = $_POST['contact'];
$address = $_POST['address'];

if (empty($_POST['id'])) {
    $stmt = $conn->prepare("INSERT INTO suppliers (name, contact, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $contact, $address);
    $stmt->execute();
    echo "Supplier added successfully!";
} else {
    $id = $_POST['id'];
    $stmt = $conn->prepare("UPDATE suppliers SET name=?, contact=?, address=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $contact, $address, $id);
    $stmt->execute();
    echo "Supplier updated successfully!";
}
?>
