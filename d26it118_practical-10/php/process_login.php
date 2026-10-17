
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Pages/login_user.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    header("Location: ../Pages/login_user.php?error=invalid");
    exit;
}

require_once __DIR__ . "/../db/db_connect_mysqli.php";

$stmt = $conn->prepare(
    "SELECT id, name, email, password, role, last_login
     FROM users WHERE email = ? LIMIT 1"
);

$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user["password"])) {
    $conn->close();
    header("Location: ../Pages/login_user.php?error=invalid");
    exit;
}

session_regenerate_id(true);

$_SESSION["user_id"] = (int) $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];
$_SESSION["last_activity"] = time();
$_SESSION["previous_login"] = $user["last_login"] ?? null;

$update = $conn->prepare(
    "UPDATE users SET last_login = NOW() WHERE id = ?"
);
$update->bind_param("i", $user["id"]);
$update->execute();
$update->close();
$conn->close();

if ($user["role"] === "admin") {
    header("Location: ../Pages/admin_dashboard.php");
} else {
    header("Location: ../Pages/student_dashboard.php");
}
exit;
