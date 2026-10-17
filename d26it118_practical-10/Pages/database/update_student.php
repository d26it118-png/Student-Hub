<?php
include "../../db/db.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $sql = "UPDATE students SET email='$email' WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        $message = mysqli_affected_rows($conn) ? "Student email updated successfully!" : "No student found or no change made.";
    } else {
        $message = "Update failed: " . mysqli_error($conn);
    }
}
?>
<h2>Update Student</h2><p><?= htmlspecialchars($message) ?></p>
<form method="POST">ID <input type="number" name="id" required><br>New Email <input type="email" name="email" required><br><button>Update</button></form>
