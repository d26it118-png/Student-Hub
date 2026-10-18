<?php

require_once __DIR__ . "/../db/db.php";

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";
    $studentId = trim($_POST["student_id"] ?? "");

    // UPDATE student
    if ($action === "update") {

        $name = trim($_POST["full_name"] ?? "");

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE students SET full_name = ? WHERE student_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ss", $name, $studentId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Student updated successfully.";
        } else {
            $message = "Update failed.";
        }

        mysqli_stmt_close($stmt);
    }

    // DELETE student
    elseif ($action === "delete") {

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM students WHERE student_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $studentId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Student deleted successfully.";
        } else {
            $message = "Delete failed.";
        }

        mysqli_stmt_close($stmt);
    }
}

// Fetch all students
$result = mysqli_query(
    $conn,
    "SELECT student_id, full_name, email, course
     FROM students
     ORDER BY student_id"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>StudentHub CRUD</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main>

        <h1>Basic MySQLi CRUD</h1>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>


        <!-- Update / Delete Form -->
        <form method="post">

            <label for="student_id">
                Student ID
            </label>

            <br>

            <input
                type="text"
                id="student_id"
                name="student_id"
                required
            >

            <br><br>


            <label for="full_name">
                New Name (for update)
            </label>

            <br>

            <input
                type="text"
                id="full_name"
                name="full_name"
            >

            <br><br>


            <button
                type="submit"
                name="action"
                value="update"
            >
                Update
            </button>


            <button
                type="submit"
                name="action"
                value="delete"
            >
                Delete
            </button>

        </form>

        <br>


        <!-- Students Table -->
        <table border="1">

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row["student_id"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["full_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["email"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["course"]); ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </main>

</body>

</html>