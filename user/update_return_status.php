<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db/connect.php';

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$request_id = $data['request_id'];

if (!isset($request_id)) {
    echo json_encode(["success" => false, "error" => "Invalid request ID"]);
    exit();
}

// Update the return status to 'returned'
$query = "UPDATE borrow_requests SET return_status = 'returned' WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $request_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Book returned successfully."]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to update return status."]);
}
?>

