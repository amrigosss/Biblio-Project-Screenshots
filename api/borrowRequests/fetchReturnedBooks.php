<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

include '../../db/connect.php';

$query = "
    SELECT br.id, u.name AS user_name, b.title AS book_title, br.request_date, 
           br.return_date, br.return_status, br.admin_remarks
    FROM borrow_requests br
    JOIN users u ON br.user_id = u.id
    JOIN books b ON br.book_id = b.id
    WHERE br.return_status = 'returned'
    ORDER BY br.return_date DESC
";

$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(["success" => true, "data" => $data]);
