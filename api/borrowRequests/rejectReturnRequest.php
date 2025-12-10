<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$request_id = $data['request_id'] ?? null;
$remarks = $data['remarks'] ?? 'Rejected';

if (!$request_id) {
    echo json_encode(["success" => false, "message" => "Invalid request ID"]);
    exit;
}

$query = "UPDATE borrow_requests SET return_status = 'rejected', admin_remarks = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $remarks, $request_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Return rejected."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to reject return."]);
}
