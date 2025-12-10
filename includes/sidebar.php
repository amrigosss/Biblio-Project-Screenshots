<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<div class="sidebar">
    <div class="sidebar-header">
        <img src="/biblio/assets/img/logo.png" alt="Biblio System Logo" class="sidebar-logo">
        <h2 class="sidebar-title">Biblio System</h2>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="<?= ($_SESSION['role'] === 'admin') ? '../admin/admin_dashboard.php' : '../user/user_dashboard.php'; ?>">
                <i class="fas fa-home"></i> <span class="menu-text">Dashboard</span>
            </a>
        </li>

        <?php if ($_SESSION['role'] === 'user'): ?>
            <li>
                <a href="../user/browse.php"><i class="fas fa-book"></i> <span class="menu-text">Browse Books</span></a>
            </li>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="../admin/manage_books.php"><i class="fas fa-tasks"></i> <span class="menu-text">Manage Books</span></a></li>
            <li><a href="../admin/manage_suppliers.php"><i class="fas fa-truck"></i> <span class="menu-text">Manage Suppliers</span></a></li>
            <li><a href="../admin/manage_users.php"><i class="fas fa-users"></i> <span class="menu-text">Manage Users</span></a></li>
            <li><a href="../admin/manage_borrow_requests.php"><i class="fas fa-list"></i> <span class="menu-text">Borrow Requests</span></a></li>
            <li><a href="../admin/manage_returns.php"><i class="fas fa-undo"></i> <span class="menu-text">Manage Returns</span></a></li>
            <li><a href="../admin/reports.php"><i class="fas fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
        <?php else: ?>
            <li><a href="../user/my_borrow_requests.php"><i class="fas fa-clock"></i> <span class="menu-text">My Borrow Requests</span></a></li>
        <?php endif; ?>

        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span class="menu-text">Logout</span></a></li>
    </ul>
</div>
