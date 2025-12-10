<?php
session_start();
include 'db/connect.php';

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if the email already exists
        $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $checkQuery->store_result();

        if ($checkQuery->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Securely hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $insert->bind_param("sss", $name, $email, $hashedPassword);

            if ($insert->execute()) {
                $success = "Registration successful! Please wait for the admin approval.";
            } else {
                $error = "Error during registration. Please try again.";
            }
        }

        $checkQuery->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - Biblio System</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <img src="assets/img/logo.png" alt="Biblio System">
        </div>
        <div class="auth-right">
            <h2>Register</h2>
            <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
            <?php if (!empty($success)): ?><p class="success"><?= $success ?></p><?php endif; ?>
            
            <form class="auth-form" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
