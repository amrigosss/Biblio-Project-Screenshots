<?php
session_start();
include '../../db/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['user_id']) || !isset($data['request_id'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized or missing data"]);
    exit();
}

$request_id = $data['request_id'];

// Only allow return for approved & borrowed items
$stmt = $conn->prepare("UPDATE borrow_requests SET return_status = 'pending return' WHERE id = ? AND user_id = ? AND (return_status IS NULL OR return_status = 'borrowed')");
$stmt->bind_param("ii", $request_id, $_SESSION['user_id']);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Return request sent"]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid or duplicate return"]);
}

$stmt->close();
$conn->close();
?>
