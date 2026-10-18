<?php
include "../../db/db.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = (int)$_POST["student_id"];
    $event_id = (int)$_POST["event_id"];
    $sql = "INSERT INTO registrations(student_id,event_id) VALUES($student_id,$event_id)";
    if (mysqli_query($conn, $sql)) {
        $message = "Event registration successful!";
    } else {
        $message = "Registration failed: " . mysqli_error($conn);
    }
}
$students = mysqli_query($conn, "SELECT id,student_id,name FROM students ORDER BY name");
$events = mysqli_query($conn, "SELECT event_id,title,event_date FROM events ORDER BY event_date");
?>
<h1>Register for Event</h1><p><?= htmlspecialchars($message) ?></p>
<form method="POST">
<select name="student_id" required><option value="">Select Student</option>
<?php while ($s = mysqli_fetch_assoc($students)): ?><option value="<?= $s["id"] ?>"><?= htmlspecialchars($s["student_id"] . " - " . $s["name"]) ?></option><?php endwhile; ?>
</select><br>
<select name="event_id" required><option value="">Select Event</option>
<?php while ($e = mysqli_fetch_assoc($events)): ?><option value="<?= $e["event_id"] ?>"><?= htmlspecialchars($e["title"] . " - " . $e["event_date"]) ?></option><?php endwhile; ?>
</select><br><button>Register</button>
</form>
