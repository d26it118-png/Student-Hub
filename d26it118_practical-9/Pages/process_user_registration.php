<?php
require_once __DIR__ . "/../db/db_connect_mysqli.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

$errors = [];

if ($name === "") {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[A-Za-z ]{2,50}$/", $name)) {
    $errors[] = "Enter a valid name using letters and spaces only.";
}

if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if ($password === "") {
    $errors[] = "Password is required.";
} elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password)) {
    $errors[] = "Password must contain 8+ characters, uppercase, lowercase, number and special character.";
}

if ($confirmPassword === "") {
    $errors[] = "Please confirm your password.";
} elseif ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

if (count($errors) > 0) {
    echo "<h2>Registration Failed</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error, ENT_QUOTES, "UTF-8") . "</li>";
    }
    echo "</ul>";
    echo '<a href="register_user.html">Go Back</a>';
    exit;
}

/* Duplicate email check before INSERT */
$checkSql = "SELECT id FROM users WHERE email = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    echo "<h2>Registration Failed</h2>";
    echo "<p>This email is already registered. Try logging in instead.</p>";
    echo '<a href="register_user.html">Go Back</a>';
    exit;
}
$checkStmt->close();

/* Never store the plain password */
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

/* Secure INSERT using a prepared statement */
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo "<h2>Registration Successful!</h2>";
    echo "<p>Your account has been registered securely using MySQLi.</p>";
    echo '<a href="register_user.html">Register Another User</a>';
} else {
    if ($conn->errno === 1062) {
        echo "<h2>Registration Failed</h2>";
        echo "<p>This email is already registered.</p>";
    } else {
        error_log("Registration database error: " . $stmt->error);
        echo "<h2>Registration Failed</h2>";
        echo "<p>Something went wrong. Please try again.</p>";
    }
}

$stmt->close();
$conn->close();
?>
