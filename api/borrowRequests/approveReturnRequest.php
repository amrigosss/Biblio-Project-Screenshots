<?php
session_start();
include '../../db/connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$request_id = $data['request_id'];

$stmt = $conn->prepare("UPDATE borrow_requests SET return_status = 'returned', return_requested = 0 WHERE id = ?");
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Return approved"]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to approve return"]);
}
?>
