<?php
// Basic MySQLi database connection for StudentHub.
$host = "localhost";
$username = "root";
$password = "";
$database = "studenthub";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
