<?php
include "../../db/db.php";
$sql = "SELECT id,student_id,name,email,mobile,course,year,gender FROM students ORDER BY id";
$result = mysqli_query($conn, $sql);
?>
<h1>Student Records</h1>
<table border="1" cellpadding="8">
<tr><th>ID</th><th>Student ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Course</th><th>Year</th><th>Gender</th></tr>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= htmlspecialchars($row["id"]) ?></td><td><?= htmlspecialchars($row["student_id"]) ?></td>
<td><?= htmlspecialchars($row["name"]) ?></td><td><?= htmlspecialchars($row["email"]) ?></td>
<td><?= htmlspecialchars($row["mobile"]) ?></td><td><?= htmlspecialchars($row["course"]) ?></td>
<td><?= htmlspecialchars($row["year"]) ?></td><td><?= htmlspecialchars($row["gender"]) ?></td>
</tr>
<?php endwhile; ?>
</table>
