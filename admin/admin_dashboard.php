<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db/connect.php';

$user_id = $_SESSION['user_id']; 
$user_role = $_SESSION['role'];

// Fetch total books
$totalBooksQuery = "SELECT COUNT(*) AS total_books FROM books";
$totalBooks = $conn->query($totalBooksQuery)->fetch_assoc()['total_books'];

// Fetch borrowed books count
if ($user_role === "admin") {
    $booksBorrowedQuery = "SELECT COUNT(*) AS books_borrowed FROM borrow_requests WHERE status = 'approved'";
} else {
    $booksBorrowedQuery = "SELECT COUNT(*) AS books_borrowed FROM borrow_requests WHERE user_id = $user_id AND status = 'approved'";
}
$booksBorrowed = $conn->query($booksBorrowedQuery)->fetch_assoc()['books_borrowed'];

// Fetch pending requests
if ($user_role === "admin") {
    $pendingRequestsQuery = "SELECT COUNT(*) AS pending_requests FROM borrow_requests WHERE status = 'pending'";
} else {
    $pendingRequestsQuery = "SELECT COUNT(*) AS pending_requests FROM borrow_requests WHERE user_id = $user_id AND status = 'pending'";
}
$pendingRequests = $conn->query($pendingRequestsQuery)->fetch_assoc()['pending_requests'];

// ✅ Fetch recent borrow requests with user name and return status
if ($user_role === "admin") {
    $recentRequestsQuery = "
        SELECT borrow_requests.id, books.title, users.name AS user_name, borrow_requests.status, borrow_requests.return_status, borrow_requests.request_date 
        FROM borrow_requests 
        JOIN books ON borrow_requests.book_id = books.id 
        JOIN users ON borrow_requests.user_id = users.id 
        ORDER BY borrow_requests.request_date DESC 
        LIMIT 5
    ";
} else {
    $recentRequestsQuery = "
        SELECT borrow_requests.id, books.title, borrow_requests.status, borrow_requests.return_status, borrow_requests.request_date 
        FROM borrow_requests 
        JOIN books ON borrow_requests.book_id = books.id 
        WHERE borrow_requests.user_id = $user_id 
        ORDER BY borrow_requests.request_date DESC 
        LIMIT 5
    ";
}
$recentRequests = $conn->query($recentRequestsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2 class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>

    <!-- Quick Stats -->
    <div class="stats-container">
        <div class="stat-box">
            <h3><?= $totalBooks ?></h3>
            <p>Total Books Available</p>
        </div>
        <div class="stat-box">
            <h3><?= $booksBorrowed ?></h3>
            <p>Books Borrowed</p>
        </div>
        <div class="stat-box">
            <h3><?= $pendingRequests ?></h3>
            <p>Pending Requests</p>
        </div>
    </div>

    <!-- Recent Borrow Requests -->
    <h3 class="section-title">Recent Borrow Requests</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <?php if ($user_role === "admin"): ?>
                    <th>User</th>
                <?php endif; ?>
                <th>Book Title</th>
                <th>Status</th>
                <th>Return Status</th>
                <th>Request Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $recentRequests->fetch_assoc()): ?>
                <tr>
                    <?php if ($user_role === "admin"): ?>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                    <td><?= ucfirst($row['return_status']) ?></td>
                    <td><?= $row['request_date'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- See All Button -->
    <?php if ($user_role === "admin"): ?>
        <div style="margin-top: 15px;">
            <a href="manage_borrow_requests.php" class="add-btn">See All Borrow Requests</a>
        </div>
    <?php else: ?>
        <div style="margin-top: 15px;">
            <a href="../user/my_borrow_requests.php" class="add-btn">See All My Requests</a>
        </div>
    <?php endif; ?>
</div>

<script src="../js/sidebar.js"></script>
</body>
</html>
