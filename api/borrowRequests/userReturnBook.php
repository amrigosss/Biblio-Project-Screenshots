<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$request_id = $data['request_id'] ?? null;

if (!$request_id) {
    echo json_encode(["success" => false, "message" => "Invalid request ID"]);
    exit();
}

// Check if return_status is already 'returned' or 'completed'
$stmt = $conn->prepare("SELECT return_status FROM borrow_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    echo json_encode(["success" => false, "message" => "Borrow request not found"]);
    exit();
}

$current_status = $result['return_status'];

if ($current_status !== 'borrowed') {
    echo json_encode(["success" => false, "message" => "This book has already been marked for return or completed"]);
    exit();
}

// Update return_status and set return_date to now
$update = $conn->prepare("UPDATE borrow_requests SET return_status = 'returned', return_date = NOW() WHERE id = ?");
$update->bind_param("i", $request_id);

if ($update->execute()) {
    echo json_encode(["success" => true, "message" => "Return submitted for admin approval"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update return status"]);
}
?>
