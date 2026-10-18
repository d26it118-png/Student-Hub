<?php
require_once __DIR__ . "/../db/db.php";

$sql = "SELECT r.registration_id, s.student_id, s.full_name,
               e.title AS event_title, e.event_date, e.venue,
               r.registration_date
        FROM registrations r
        INNER JOIN students s ON r.student_id = s.student_id
        INNER JOIN events e ON r.event_id = e.event_id
        ORDER BY r.registration_id DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Hub - Registrations</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
<a href="index.html">Home</a>
<a href="events.php">Events</a>
<a href="students.php">Students</a>
<a href="registrations.php">Registrations</a>
</nav>
<main>
<h1>Event Registrations</h1>
<p class="intro-text">This page demonstrates an INNER JOIN between students, events and registrations.</p>
<table border="1">
<thead>
<tr><th>ID</th><th>Student ID</th><th>Student Name</th><th>Event</th><th>Event Date</th><th>Venue</th><th>Registered On</th></tr>
</thead>
<tbody>
<?php if ($result && mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?php echo htmlspecialchars($row["registration_id"]); ?></td>
<td><?php echo htmlspecialchars($row["student_id"]); ?></td>
<td><?php echo htmlspecialchars($row["full_name"]); ?></td>
<td><?php echo htmlspecialchars($row["event_title"]); ?></td>
<td><?php echo htmlspecialchars($row["event_date"]); ?></td>
<td><?php echo htmlspecialchars($row["venue"]); ?></td>
<td><?php echo htmlspecialchars($row["registration_date"]); ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7">No registrations found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</main>
</body>
</html>
<?php mysqli_close($conn); ?>
