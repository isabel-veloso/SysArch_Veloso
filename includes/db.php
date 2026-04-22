<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$host     = "localhost";
$user     = "root";
$password = "";
$database = "sit_in_db";

// Connect to MySQL
$conn = mysqli_connect($host, $user, $password, $database);

// Stop everything if connection fails
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>