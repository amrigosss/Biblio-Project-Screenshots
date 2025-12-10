<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../db/connect.php';
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/manage_users.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Manage Users</h2>
    <button class="add-btn" onclick="openUserModal()">+ Add User</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Access</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($user['role'])) ?></td>
                    <td>
                        <select onchange="updateStatus(<?= $user['id'] ?>, this.value)">
                            <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $user['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </td>
                    <td>
                        <select onchange="updateAccess(<?= $user['id'] ?>, this.value)">
                            <option value="active" <?= $user['access'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="disabled" <?= $user['access'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </td>
                    <td>
                        <button class="edit-btn" onclick="editUser(
                            <?= $user['id'] ?>, 
                            '<?= htmlspecialchars(addslashes($user['name'])) ?>', 
                            '<?= htmlspecialchars(addslashes($user['email'])) ?>', 
                            '<?= $user['role'] ?>'
                        )">Edit</button>
                        <button class="delete-btn" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUserModal()">&times;</span>
        <h3 id="modalTitle">Add User</h3>
        <form id="userForm">
            <input type="hidden" id="userId">

            <label for="name">Full Name:</label>
            <input type="text" id="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" required>

            <label for="role">Role:</label>
            <select id="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script src="../js/manage_users.js"></script>
<script src="../js/sidebar.js"></script>

<script>
function updateStatus(userId, newStatus) {
    fetch("../api/users/updateStatus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${userId}&status=${newStatus}`
    })
    .then(res => res.text())
    .then(data => {
        alert(data.trim());
    })
    .catch(err => {
        console.error("Status update failed:", err);
        alert("Error updating user status.");
    });
}

function updateAccess(userId, newAccess) {
    fetch("../api/users/toggleUserStatus.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id=${userId}&access=${newAccess}`
    })
    .then(res => res.text())
    .then(data => {
        alert(data.trim());
    })
    .catch(err => {
        console.error("Access update failed:", err);
        alert("Error updating user access.");
    });
}
</script>

</body>
</html>
