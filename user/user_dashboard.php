<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include '../db/connect.php';

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Quick stats
$total_books = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$books_borrowed = $conn->query("SELECT COUNT(*) AS total FROM borrow_requests WHERE user_id = $user_id AND status='approved'")->fetch_assoc()['total'];
$pending_requests = $conn->query("SELECT COUNT(*) AS total FROM borrow_requests WHERE user_id = $user_id AND status='pending'")->fetch_assoc()['total'];

// Fetch recent borrow requests
$recent_requests = $conn->prepare("
    SELECT b.title, br.status, br.return_status, br.request_date 
    FROM borrow_requests br 
    JOIN books b ON br.book_id = b.id 
    WHERE br.user_id = ? 
    ORDER BY br.request_date DESC 
    LIMIT 5
");
$recent_requests->bind_param("i", $user_id);
$recent_requests->execute();
$requests_result = $recent_requests->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/user_dashboard.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Welcome, <?= htmlspecialchars($name) ?>!</h2>
    <div class="stats-container">
        <div class="stat-box"><h3><?= $total_books ?></h3><p>Total Books Available</p></div>
        <div class="stat-box"><h3><?= $books_borrowed ?></h3><p>Books Borrowed</p></div>
        <div class="stat-box"><h3><?= $pending_requests ?></h3><p>Pending Requests</p></div>
    </div>

    <h3>Recent Borrow Requests</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Return Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $requests_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['request_date'] ?></td>
                    <td><?= ($row['return_status'] === 'returned') ? 'Returned' : 'Not returned' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 15px;">
        <a href="my_borrow_requests.php" class="add-btn">See All My Requests</a>
    </div>
</div>

<script src="../js/sidebar.js"></script>
</body>
</html>
