<?php
$host = "localhost";
$username = "root"; // Default in XAMPP
$password = "";     // Default is empty
$dbname = "biblio";
$port = 3307;       // ✅ Add this line for the correct port

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

