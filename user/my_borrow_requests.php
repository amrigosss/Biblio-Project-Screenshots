<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../db/connect.php';

$user_id = $_SESSION['user_id'];

// FILTER ONLY NOT RETURNED
$query = "SELECT br.id, b.title AS book_title, br.status, br.request_date, br.return_status
          FROM borrow_requests br
          JOIN books b ON br.book_id = b.id
          WHERE br.user_id = ?
          ORDER BY br.request_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Borrow Requests</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/borrow_requests.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2 class="page-title">My Borrow Requests</h2>
    <label style="display: inline-block; margin-bottom: 10px;">
        <input type="checkbox" id="showActiveOnly" onchange="filterRows()">
        Show only not returned
    </label>
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Book Title</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Return Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['book_title']) ?></td>
                    <td class="status-<?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
                    <td><?= $row['request_date'] ?></td>
                    <td><?= ucfirst($row['return_status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'approved' && $row['return_status'] === 'borrowed'): ?>
                            <button class="return-btn" onclick="returnBook(<?= $row['id'] ?>)">🔄 Return</button>
                        <?php elseif ($row['return_status'] === 'pending return'): ?>
                            <span class="pending-status">⏳ Awaiting Approval</span>
                        <?php elseif ($row['return_status'] === 'returned'): ?>
                            <span class="returned-status">✔ Returned</span>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function returnBook(requestId) {
    if (confirm("Are you sure you want to return this book?")) {
        fetch("../api/borrowRequests/requestReturn.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ request_id: requestId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        })
        .catch(error => console.error("Fetch Error:", error));
    }
}
</script>
<script>
function filterRows() {
    const checkbox = document.getElementById("showActiveOnly");
    const rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        const returnStatus = row.children[4].textContent.trim().toLowerCase();
        row.style.display = checkbox.checked && returnStatus === "returned" ? "none" : "";

    });
}
</script>

</body>
</html>
