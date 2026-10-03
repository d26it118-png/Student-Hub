<?php
require_once "db.php";

$result = mysqli_query($conn, "SELECT 1 AS test_value");

if ($result) {
    echo "<h2>Database connection successful!</h2>";
    echo "<p>StudentHub MySQL database is connected using basic MySQLi.</p>";
} else {
    echo "<h2>Query failed.</h2>";
    echo "<p>" . htmlspecialchars(mysqli_error($conn)) . "</p>";
}

mysqli_close($conn);
?>
