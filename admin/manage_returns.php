<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Approvals</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/manage_returns.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Returned Books – Admin Approval</h2>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>User</th>
                <th>Book</th>
                <th>Request Date</th>
                <th>Return Date</th>
                <th>Return Status</th>
                <th>Admin Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="returnedBooksTableBody">
            <!-- Data will be loaded here -->
        </tbody>
    </table>
</div>

<script>
function loadReturnedBooks() {
    fetch("../api/borrowRequests/fetchReturnedBooks.php")
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('returnedBooksTableBody');
            tbody.innerHTML = '';

            if (data.success) {
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.user_name}</td>
                        <td>${row.book_title}</td>
                        <td>${row.request_date}</td>
                        <td>${row.return_date ?? '-'}</td>
                        <td>${row.return_status}</td>
                        <td>${row.admin_remarks ?? '-'}</td>
                        <td>
                            <button onclick="approveReturn(${row.id})">✅ Approve</button>
                            <button onclick="declineReturn(${row.id})">❌ Reject</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                alert("Failed to fetch data: " + data.message);
            }
        });
}

function approveReturn(requestId) {
    if (confirm("Approve this return?")) {
        fetch("../api/borrowRequests/approveReturnRequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ request_id: requestId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadReturnedBooks();
        });
    }
}

function declineReturn(requestId) {
    const reason = prompt("Enter reason for rejecting:");
    if (reason) {
        fetch("../api/borrowRequests/rejectReturnRequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ request_id: requestId, remarks: reason })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadReturnedBooks();
        });
    }
}

window.onload = loadReturnedBooks;
</script>

</body>
</html>
