<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../db/connect.php';

// Fetch suppliers
$suppliers = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Suppliers</title>
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/manage_suppliers.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Manage Suppliers</h2>
    <button class="add-btn" onclick="openAddSupplierModal()">+ Add Supplier</button>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                <tr>
                    <td><?= $supplier['id'] ?></td>
                    <td><?= htmlspecialchars($supplier['name']) ?></td>
                    <td><?= htmlspecialchars($supplier['contact']) ?></td>
                    <td><?= htmlspecialchars($supplier['address']) ?></td>
                    <td>
                        <button onclick="openEditSupplierModal(<?= $supplier['id'] ?>, '<?= htmlspecialchars($supplier['name']) ?>', '<?= htmlspecialchars($supplier['contact']) ?>', '<?= htmlspecialchars($supplier['address']) ?>')">Edit</button>
                        <button onclick="deleteSupplier(<?= $supplier['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Supplier Modal -->
<div id="supplierModal" class="modal">
    <div class="modal-content">
        <span onclick="closeSupplierModal()" class="close">&times;</span>
        <h3 id="modalTitle">Add Supplier</h3>
        <form id="supplierForm">
            <input type="hidden" id="supplierId">
            <input type="text" id="supplierName" placeholder="Supplier Name" required>
            <input type="text" id="supplierContact" placeholder="Contact Info" required>
            <input type="text" id="supplierAddress" placeholder="Address" required>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script src="../js/manage_suppliers.js"></script>
</body>
</html>
