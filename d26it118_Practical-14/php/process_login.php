
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
     FROM users
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log("Login query preparation failed: " . $conn->error);
    $conn->close();
    die("A database error occurred. Please try again.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$passwordMatches = false;
$needsRehash = false;

if ($user) {
    $storedPassword = (string) $user["password"];

    // Normal secure password verification.
    if (password_verify($password, $storedPassword)) {
        $passwordMatches = true;

        // Upgrade older password hashes when needed.
        $needsRehash = password_needs_rehash(
            $storedPassword,
            PASSWORD_DEFAULT
        );
    } elseif (hash_equals($storedPassword, $password)) {
        // Temporary compatibility for legacy plain-text passwords.
        $passwordMatches = true;
        $needsRehash = true;
    }
}

if (!$user || !$passwordMatches) {
    $conn->close();
    header("Location: ../Pages/login_user.php?error=invalid");
    exit;
}

// Convert a legacy plain-text password to a secure hash after login.
if ($needsRehash) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $updatePassword = $conn->prepare(
        "UPDATE users SET password = ? WHERE id = ?"
    );

    if ($updatePassword) {
        $updatePassword->bind_param("si", $newHash, $user["id"]);
        $updatePassword->execute();
        $updatePassword->close();
    } else {
        error_log("Password hash update failed: " . $conn->error);
    }
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

if ($update) {
    $update->bind_param("i", $user["id"]);
    $update->execute();
    $update->close();
}

$conn->close();

if ($user["role"] === "admin") {
    header("Location: ../Pages/admin_dashboard.php");
} else {
    header("Location: ../Pages/student_dashboard.php");
}

exit;
?>
