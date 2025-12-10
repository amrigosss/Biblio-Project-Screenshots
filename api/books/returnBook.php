<?php
session_start();
include '../../db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Decode JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $borrow_id = filter_var($data['borrow_id'], FILTER_VALIDATE_INT);

    // Validate input
    if ($borrow_id === false) {
        echo json_encode(["status" => "error", "message" => "Invalid borrow ID"]);
        exit();
    }

    // Get the book_id before updating
    $check_stmt = $conn->prepare("SELECT book_id FROM borrow_requests WHERE id = ?");
    $check_stmt->bind_param("i", $borrow_id);
    $check_stmt->execute();
    $check_stmt->bind_result($book_id);
    if (!$check_stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Borrow request not found"]);
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    // Update borrow request status to "returned"
    $stmt = $conn->prepare("UPDATE borrow_requests SET return_status = 'returned' WHERE id = ?");
    $stmt->bind_param("i", $borrow_id);

    if ($stmt->execute()) {
        // Increase book stock
        $updateStock = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
        $updateStock->bind_param("i", $book_id);
        $updateStock->execute();
        $updateStock->close();

        echo json_encode(["status" => "success", "message" => "Book returned successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to return book: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
