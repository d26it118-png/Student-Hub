<?php
// Basic MySQL connection using MySQLi (no PDO)
$host = "localhost";
$user = "root";
$password = "";
$database = "studenthub";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
