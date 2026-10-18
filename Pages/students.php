
<?php
require_once __DIR__ . "/../db/db.php";

// This file expects db.php to provide a MySQLi connection named $conn.
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection failed. Check ../db/db.php");
}

$message = "";
$messageType = "success";

// Escape output safely
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Delete student
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_student"])) {

    $studentId = trim($_POST["student_id"] ?? "");

    if ($studentId !== "") {
        $stmt = $conn->prepare(
            "DELETE FROM students WHERE student_id = ?"
        );
        $stmt->bind_param("s", $studentId);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "Student deleted successfully.";
            } else {
                $message = "Student not found.";
                $messageType = "error";
            }
        } else {
            $message = "Failed to delete student.";
            $messageType = "error";
        }

        $stmt->close();
    }

    // Prevent the browser from resubmitting the delete form.
    header("Location: students.php?message=" .
        urlencode($message) . "&type=" . $messageType);
    exit;
}

// Update student
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["update_student"])) {

    $studentId = trim($_POST["student_id"] ?? "");
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = filter_var($_POST["year"] ?? "", FILTER_VALIDATE_INT);
    $gender = trim($_POST["gender"] ?? "");

    if (
        $studentId === "" || $fullName === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $course === "" || $year === false || $year < 1
    ) {
        $message = "Please enter valid student details.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare(
            "UPDATE students
             SET full_name = ?, email = ?, mobile = ?,
                 course = ?, year = ?, gender = ?
             WHERE student_id = ?"
        );

        $stmt->bind_param(
            "ssssiss",
            $fullName,
            $email,
            $mobile,
            $course,
            $year,
            $gender,
            $studentId
        );

        if ($stmt->execute()) {
            $message = "Student updated successfully.";
        } else {
            if ($stmt->errno == 1062) {
                $message = "This email is already used by another student.";
            } else {
                $message = "Failed to update student.";
            }
            $messageType = "error";
        }

        $stmt->close();
    }

    if ($messageType === "success") {
        header("Location: students.php?message=" .
            urlencode($message) . "&type=success");
        exit;
    }
}

// Display message after redirect
if (isset($_GET["message"])) {
    $message = (string)$_GET["message"];
    $messageType = ($_GET["type"] ?? "success") === "error"
        ? "error" : "success";
}

// Load student for editing
$editStudent = null;

if (isset($_GET["edit"]) && $_GET["edit"] !== "") {
    $editId = (string)$_GET["edit"];

    $stmt = $conn->prepare(
        "SELECT student_id, full_name, email, mobile,
                course, year, gender
         FROM students WHERE student_id = ?"
    );
    $stmt->bind_param("s", $editId);
    $stmt->execute();

    $result = $stmt->get_result();
    $editStudent = $result->fetch_assoc();
    $stmt->close();

    if (!$editStudent) {
        $message = "Student not found.";
        $messageType = "error";
    }
}

// Search students
$search = trim($_GET["search"] ?? "");

if ($search !== "") {
    $stmt = $conn->prepare(
        "SELECT student_id, full_name, email, mobile,
                course, year, gender
         FROM students
         WHERE student_id LIKE ?
            OR full_name LIKE ?
            OR email LIKE ?
            OR course LIKE ?
         ORDER BY student_id"
    );

    $like = "%" . $search . "%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    $stmt = $conn->prepare(
        "SELECT student_id, full_name, email, mobile,
                course, year, gender
         FROM students
         ORDER BY student_id"
    );
}

$stmt->execute();
$students = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profiles | StudentHub</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f0f3ff;
            color: #243247;
        }

        nav {
            margin-top: 70px;
            padding: 25px 12px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
            background: linear-gradient(100deg, #8958fa, #20c9df);
            border-radius: 14px;
        }

        nav a {
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .container {
            max-width: 1250px;
            margin: 28px auto;
            padding: 35px;
            background: white;
            border-radius: 30px;
            box-shadow: 0 12px 40px #dce2f5;
        }

        h1 {
            color: #192d47;
            margin-top: 0;
        }

        .subtitle {
            color: #52637a;
            margin-bottom: 28px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin: 25px 0;
            align-items: center;
        }

        input, select {
            padding: 12px;
            border: 1px solid #cbd2df;
            border-radius: 8px;
            font-size: 15px;
        }

        .search-input {
            width: min(100%, 565px);
        }

        button, .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 7px;
            color: white;
            background: #6555e8;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-add {
            background: #13a66b;
        }

        .btn-edit {
            background: #ed9a19;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-cancel {
            background: #667085;
        }

        .btn:hover, button:hover {
            opacity: 0.85;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 900px;
        }

        th {
            color: white;
            background: linear-gradient(100deg, #8958fa, #20c9df);
        }

        th, td {
            padding: 15px 12px;
            text-align: center;
            border: 1px solid #d2d7e0;
        }

        tbody tr:hover {
            background: #f6f8ff;
        }

        .actions {
            display: flex;
            gap: 7px;
            justify-content: center;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .message {
            padding: 13px;
            border-radius: 8px;
            margin: 18px 0;
            background: #e3f8eb;
            color: #146c39;
        }

        .message.error {
            background: #ffe7e7;
            color: #a21d2b;
        }

        .edit-box {
            background: #f8f9ff;
            border: 1px solid #dfe3f5;
            border-radius: 14px;
            padding: 22px;
            margin: 25px 0;
        }

        .edit-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field input, .field select {
            width: 100%;
        }

        .edit-buttons {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .empty {
            padding: 25px;
            text-align: center;
        }

        @media (max-width: 650px) {
            .container {
                margin: 15px;
                padding: 18px;
            }

            nav {
                gap: 15px;
            }

            .edit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<nav>
    <a href="../index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="events.php">Events</a>
    <a href="students.php">Students</a>
    <a href="profile.php">Profile</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact</a>
</nav>

<main class="container">

    <h1>Student Profiles</h1>
    <p class="subtitle">
        View, add, edit, delete, search and manage student records.
    </p>

    <?php if ($message !== ""): ?>
        <div class="message <?= $messageType === "error" ? "error" : "" ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($editStudent): ?>
        <section class="edit-box">
            <h2>Edit Student</h2>

            <form method="POST" action="students.php">
                <input type="hidden" name="student_id"
                       value="<?= e($editStudent["student_id"]) ?>">

                <div class="edit-grid">
                    <div class="field">
                        <label>Student ID</label>
                        <input type="text"
                               value="<?= e($editStudent["student_id"]) ?>"
                               disabled>
                    </div>

                    <div class="field">
                        <label for="full_name">Full Name *</label>
                        <input id="full_name" name="full_name"
                               maxlength="100" required
                               value="<?= e($editStudent["full_name"]) ?>">
                    </div>

                    <div class="field">
                        <label for="email">Email *</label>
                        <input id="email" type="email" name="email"
                               maxlength="100" required
                               value="<?= e($editStudent["email"]) ?>">
                    </div>

                    <div class="field">
                        <label for="mobile">Mobile</label>
                        <input id="mobile" type="text" name="mobile"
                               maxlength="15"
                               value="<?= e($editStudent["mobile"] ?? "") ?>">
                    </div>

                    <div class="field">
                        <label for="course">Course *</label>
                        <input id="course" name="course"
                               maxlength="50" required
                               value="<?= e($editStudent["course"]) ?>">
                    </div>

                    <div class="field">
                        <label for="year">Year *</label>
                        <select id="year" name="year" required>
                            <?php for ($y = 1; $y <= 4; $y++): ?>
                                <option value="<?= $y ?>"
                                    <?= (int)$editStudent["year"] === $y
                                        ? "selected" : "" ?>>
                                    <?= $y ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <?php
                            $genders = ["Male", "Female", "Other"];
                            foreach ($genders as $g):
                            ?>
                                <option value="<?= e($g) ?>"
                                    <?= strcasecmp(
                                        (string)($editStudent["gender"] ?? ""),
                                        $g
                                    ) === 0 ? "selected" : "" ?>>
                                    <?= e($g) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="edit-buttons">
                    <button type="submit" name="update_student">
                        Save Changes
                    </button>

                    <a class="btn btn-cancel" href="students.php">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <div class="toolbar">
        <form method="GET" action="students.php">
            <input class="search-input"
                   type="search"
                   name="search"
                   placeholder="Search by name, ID, course or email"
                   value="<?= e($search) ?>">
            <button type="submit">Search</button>

            <?php if ($search !== ""): ?>
                <a class="btn btn-cancel" href="students.php">Clear</a>
            <?php endif; ?>
        </form>

        <a class="btn btn-add" href="register.php">
            + Add Student
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($students->num_rows > 0): ?>
                    <?php while ($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($row["student_id"]) ?></td>
                            <td><?= e($row["full_name"]) ?></td>
                            <td><?= e($row["email"]) ?></td>
                            <td><?= e($row["course"]) ?></td>
                            <td><?= e($row["year"]) ?></td>

                            <td>
                                <div class="actions">
                                    <a class="btn btn-edit"
                                       href="students.php?edit=<?= urlencode($row["student_id"]) ?>">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="students.php"
                                          onsubmit="return confirm('Are you sure you want to delete this student? This may also delete their event registrations.');">
                                        <input type="hidden"
                                               name="student_id"
                                               value="<?= e($row["student_id"]) ?>">

                                        <button class="btn-delete"
                                                type="submit"
                                                name="delete_student">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty">
                            No student records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>

<?php
$stmt->close();
?>
