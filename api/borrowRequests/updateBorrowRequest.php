<?php
session_start();
header('Content-Type: application/json');

// --- Session & Role Check ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

// --- DB Connection ---
include '../../db/connect.php'; // Adjust path if needed

// --- Parse JSON Input ---
$data = json_decode(file_get_contents("php://input"), true);

// --- Log Incoming Data ---
file_put_contents("log.txt", "Input: " . json_encode($data) . "\n", FILE_APPEND);

// --- Validate Input ---
if (!isset($data['request_id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Invalid request data"]);
    exit();
}

$request_id = intval($data['request_id']);
$status = strtolower(trim($data['status']));

// --- Validate Status ---
$valid_statuses = ['approved', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    file_put_contents("log.txt", "Invalid status received: $status\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Invalid status value"]);
    exit();
}

// --- Prepare SQL Update ---
$stmt = $conn->prepare("UPDATE borrow_requests SET status = ? WHERE id = ?");
if (!$stmt) {
    file_put_contents("log.txt", "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Prepare failed"]);
    exit();
}

$stmt->bind_param("si", $status, $request_id);

// --- Execute and Respond ---
if ($stmt->execute()) {
    file_put_contents("log.txt", "Update successful for ID $request_id\n", FILE_APPEND);
    echo json_encode(["success" => true, "message" => "Borrow request updated successfully"]);
} else {
    file_put_contents("log.txt", "Execute failed: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "error" => "Failed to update request"]);
}

// --- Cleanup ---
$stmt->close();
$conn->close();
