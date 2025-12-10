<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Updated to include access check
    $stmt = $conn->prepare("SELECT id, name, password, role, status, access FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password, $role, $status, $access);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if ($access === "disabled") {
            $error = "Your account has been disabled. Please contact the administrator.";
        } elseif ($status !== "approved") {
            $error = "Your account is pending approval. Please wait for an admin to approve it.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === "admin") {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: user/user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } else {
        $error = "User not found. Please check your credentials.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-left">
        <img src="assets/img/logo.png" alt="Biblio System">
    </div>
    <div class="auth-right">
        <h2>Login</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</div>

</body>
</html>
