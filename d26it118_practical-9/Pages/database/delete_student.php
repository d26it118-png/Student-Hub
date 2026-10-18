<?php
include "../../db/db.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $sql = "DELETE FROM students WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        $message = mysqli_affected_rows($conn) ? "Student deleted successfully!" : "No student found.";
    } else {
        $message = "Delete failed: " . mysqli_error($conn);
    }
}
?>
<h2>Delete Student</h2><p><?= htmlspecialchars($message) ?></p>
<form method="POST">ID <input type="number" name="id" required><br><button>Delete</button></form>
