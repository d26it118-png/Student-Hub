<?php
include "../../db/db.php";
$sql = "SELECT r.registration_id, s.student_id, s.name, e.title AS event_name,
               e.category, e.event_date, e.venue, r.registered_at
        FROM registrations r
        INNER JOIN students s ON r.student_id = s.id
        INNER JOIN events e ON r.event_id = e.event_id
        ORDER BY r.registration_id";
$result = mysqli_query($conn, $sql);
?>
<h1>Event Registrations</h1>
<table border="1" cellpadding="8">
<tr><th>Reg ID</th><th>Student ID</th><th>Name</th><th>Event</th><th>Category</th><th>Date</th><th>Venue</th><th>Registered</th></tr>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<?php foreach ($row as $value): ?><td><?= htmlspecialchars($value) ?></td><?php endforeach; ?>
</tr>
<?php endwhile; ?>
</table>
