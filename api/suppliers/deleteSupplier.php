<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

$conn->query("DELETE FROM suppliers WHERE id = $id");
echo "Supplier deleted successfully!";
?>
