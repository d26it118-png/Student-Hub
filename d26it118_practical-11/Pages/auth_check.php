<?php
// Configure session cookies before starting the session. Use HTTPS in production.
$isHttps = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => $isHttps,
    "httponly" => true,
    "samesite" => "Lax"
]);
session_start();

require_once __DIR__ . "/../db/db_connect_mysqli.php";

// Optional remember-me restoration when the session is not already authenticated.
if (!isset($_SESSION["user_id"]) && !empty($_COOKIE["remember_token"])) {
    $tokenHash = hash("sha256", $_COOKIE["remember_token"]);
    $stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.role, t.token_id FROM auth_tokens t JOIN users u ON u.id = t.user_id WHERE t.token_hash = ? AND t.expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $remembered = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($remembered) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int)$remembered["id"];
        $_SESSION["user_name"] = $remembered["name"];
        $_SESSION["user_email"] = $remembered["email"];
        $_SESSION["user_role"] = $remembered["role"] ?: "student";
        $_SESSION["last_activity"] = time();
    } else {
        setcookie("remember_token", "", time() - 3600, "/");
    }
}

// Every protected page must check login and inactivity timeout.
if (!isset($_SESSION["user_id"])) {
    header("Location: login_user.php");
    exit;
}

$timeoutSeconds = 900; // 15 minutes
if (!isset($_SESSION["last_activity"]) || (time() - $_SESSION["last_activity"]) > $timeoutSeconds) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
    header("Location: login_user.php?error=timeout");
    exit;
}
$_SESSION["last_activity"] = time();

function require_role(string $role): void {
    if (($_SESSION["user_role"] ?? "") !== $role) {
        if (($_SESSION["user_role"] ?? "") === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    }
}
?>
