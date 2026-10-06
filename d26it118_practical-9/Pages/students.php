<?php
require_once __DIR__ . "/../db/db.php";
$sql = "SELECT student_id, full_name, email, course, year FROM students ORDER BY student_id";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Hub - Students</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<nav>
<a href="index.html">Home</a>
<a href="dashboard.html">Dashboard</a>
<a href="events.php">Events</a>
<a href="students.php">Students</a>
<a href="profile.html">Profile</a>
<a href="faq.html">FAQ</a>
<a href="contact.html">Contact</a>
</nav>
<main>
<h1>Student Profiles</h1>
<p class="intro-text">Student records are fetched from MySQL using basic MySQLi queries.</p>
<div class="data-controls">
<input id="studentSearch" type="search" placeholder="Search by name, ID, course or email">
</div>
<table border="1" id="studentTable">
<thead><tr><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th></tr></thead>
<tbody>
<?php if ($result && mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td><?php echo htmlspecialchars($row["student_id"]); ?></td>
<td><?php echo htmlspecialchars($row["full_name"]); ?></td>
<td><?php echo htmlspecialchars($row["email"]); ?></td>
<td><?php echo htmlspecialchars($row["course"]); ?></td>
<td><?php echo htmlspecialchars($row["year"]); ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5">No students found.</td></tr>
<?php endif; ?>
</tbody>
</table>
<p><a href="register.php">Register a new student</a></p>
</main>
<script>
const search = document.getElementById("studentSearch");
search.addEventListener("input", function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll("#studentTable tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
});
</script>
</body>
</html>
<?php mysqli_close($conn); ?>
