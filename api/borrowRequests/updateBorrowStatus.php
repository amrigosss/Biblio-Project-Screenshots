<?php
// DEBUG: Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "error" => "Unauthorized access"
    ]);
    exit();
}

header('Content-Type: application/json');

// Connect to database
include '../db/connect.php';

// Get the raw POST data (JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid input"
    ]);
    exit();
}

$request_id = isset($input['request_id']) ? intval($input['request_id']) : 0;
$status = isset($input['status']) ? strtolower(trim($input['status'])) : '';

// Validate inputs
$valid_statuses = ['approved', 'rejected'];

if ($request_id <= 0 || !in_array($status, $valid_statuses)) {
    echo json_encode([
        "success" => false,
        "error" => "Invalid request ID or status"
    ]);
    exit();
}

// Prepare and run the update query
$stmt = $conn->prepare("UPDATE borrow_requests SET status = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error" => "Database prepare error: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("si", $status, $request_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Request status updated to " . ucfirst($status)
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "No matching borrow request found or status already set"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "error" => "Database execute error: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
