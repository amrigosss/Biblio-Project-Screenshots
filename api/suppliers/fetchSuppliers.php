<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

include '../../db/connect.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM suppliers WHERE id = $id");

if ($supplier = $result->fetch_assoc()) {
    echo json_encode($supplier);
} else {
    echo json_encode([]);
}
?>
