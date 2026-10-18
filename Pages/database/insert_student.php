<?php
include "../../db/db.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = mysqli_real_escape_string($conn, trim($_POST["student_id"]));
    $name = mysqli_real_escape_string($conn, trim($_POST["name"]));
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $mobile = mysqli_real_escape_string($conn, trim($_POST["mobile"]));
    $course = mysqli_real_escape_string($conn, trim($_POST["course"]));
    $year = (int)$_POST["year"];
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);

    $sql = "INSERT INTO students(student_id,name,email,mobile,course,year,gender)
            VALUES('$student_id','$name','$email','$mobile','$course',$year,'$gender')";

    if (mysqli_query($conn, $sql)) {
        $message = "Student inserted successfully!";
    } else {
        $message = "Insert failed: " . mysqli_error($conn);
    }
}
?>
<h2>Insert Student</h2>
<p><?= htmlspecialchars($message) ?></p>
<form method="POST">
Student ID <input name="student_id" required><br>
Name <input name="name" required><br>
Email <input type="email" name="email" required><br>
Mobile <input name="mobile" required><br>
Course <input name="course" required><br>
Year <input type="number" name="year" min="1" max="4" required><br>
Gender <select name="gender" required><option>Male</option><option>Female</option><option>Other</option></select><br>
<button>Save</button>
</form>
