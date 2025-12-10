<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db/connect.php';

// Fetch stats
$totalBooksQuery = "SELECT COUNT(*) AS total_books FROM books";
$booksBorrowedQuery = "SELECT COUNT(*) AS books_borrowed FROM borrow_requests WHERE status = 'approved'";
$pendingRequestsQuery = "SELECT COUNT(*) AS pending_requests FROM borrow_requests WHERE status = 'pending'";

$totalBooks = $conn->query($totalBooksQuery)->fetch_assoc()['total_books'];
$booksBorrowed = $conn->query($booksBorrowedQuery)->fetch_assoc()['books_borrowed'];
$pendingRequests = $conn->query($pendingRequestsQuery)->fetch_assoc()['pending_requests'];

// Fetch top borrowed books
$topBooksQuery = "SELECT books.title, COUNT(borrow_requests.id) AS borrow_count 
                  FROM borrow_requests 
                  JOIN books ON borrow_requests.book_id = books.id 
                  WHERE borrow_requests.status = 'approved' 
                  GROUP BY books.title 
                  ORDER BY borrow_count DESC 
                  LIMIT 5";
$topBooksResult = $conn->query($topBooksQuery);

// 🔄 Fetch borrowing data this current month (for donut chart)
$currentMonth = date('Y-m');
$bookTrendsQuery = "
    SELECT books.title, COUNT(borrow_requests.id) AS borrow_count
    FROM borrow_requests
    JOIN books ON borrow_requests.book_id = books.id
    WHERE borrow_requests.status = 'approved' 
      AND DATE_FORMAT(borrow_requests.request_date, '%Y-%m') = '$currentMonth'
    GROUP BY books.title
";
$bookTrendsResult = $conn->query($bookTrendsQuery);

$bookTrendLabels = [];
$bookTrendCounts = [];
while ($row = $bookTrendsResult->fetch_assoc()) {
    $bookTrendLabels[] = $row['title'];
    $bookTrendCounts[] = $row['borrow_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Reports</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2 class="page-title">Library Reports</h2>

    <!-- Quick Stats -->
    <div class="stats-container">
        <div class="stat-box">
            <h3><?= $totalBooks ?></h3>
            <p>Total Books</p>
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

    <!-- Top Borrowed Books -->
    <h3 class="section-title">Top Borrowed Books</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Borrow Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $topBooksResult->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['borrow_count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- 📊 Donut Chart for Book Trends This Month -->
    <h3 class="section-title">Book Trends This Month</h3>
    <canvas id="borrowChart" style="max-width: 600px; height: 400px;"></canvas>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("borrowChart").getContext("2d");

    const labels = <?= json_encode($bookTrendLabels) ?>;
    const counts = <?= json_encode($bookTrendCounts) ?>;

    const colors = labels.map(() =>
        `hsl(${Math.floor(Math.random() * 360)}, 70%, 60%)`
    );

    const labelsWithCounts = labels.map((title, i) => {
    const maxLength = 25;
    const shortTitle = title.length > maxLength ? title.substring(0, maxLength) + '…' : title;
    return `${shortTitle} (${counts[i]}x)`;
});


    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: labelsWithCounts,
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderColor: "#fff",
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "right",
                    labels: {
                        font: { size: 14 },
                        color: "#333"
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `${context.label}`;
                        }
                    }
                }
            }
        }
    });
});
</script>






</body>
</html>
