<?php
// DEBUG: Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Connect to database
include '../db/connect.php';

// Fetch borrow requests with error handling
$query = "SELECT borrow_requests.id, users.name AS user_name, books.title AS book_title, borrow_requests.status, borrow_requests.request_date 
          FROM borrow_requests
          JOIN users ON borrow_requests.user_id = users.id
          JOIN books ON borrow_requests.book_id = books.id
          ORDER BY borrow_requests.id ASC";

$result = $conn->query($query);

// Check SQL error
if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Borrow Requests</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/manage_borrow_requests.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2 class="page-title">Manage Borrow Requests</h2>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Book</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['book_title']) ?></td>
                    <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['request_date'] ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <button class="approve-btn" data-id="<?= $row['id'] ?>">✅ Approve</button>
                            <button class="reject-btn" data-id="<?= $row['id'] ?>">✖ Reject</button>

                        <?php else: ?>
                            <?php if ($row['status'] === 'approved'): ?>
                                <span class="approved-label">✅ Approved</span>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                                <span class="rejected-label">❌ Rejected</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="../js/manage_borrow_requests.js"></script>
</body>
</html>
