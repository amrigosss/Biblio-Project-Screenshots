<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['request_id'])) {
    echo json_encode(["success" => false, "error" => "Invalid request ID"]);
    exit();
}

$request_id = $data['request_id'];

// ✅ Update to 'pending return' instead of 'returned'
$update = $conn->prepare("UPDATE borrow_requests SET return_status = 'pending return' WHERE id = ?");
$update->bind_param("i", $request_id);

if ($update->execute()) {
    echo json_encode(["success" => true, "message" => "Return request submitted. Awaiting admin approval."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to update return status."]);
}

$update->close();
$conn->close();
?>
