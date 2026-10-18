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

// Revoke the remember-me token, if this browser has one.
if (!empty($_COOKIE["remember_token"])) {
    $tokenHash = hash("sha256", $_COOKIE["remember_token"]);
    $stmt = $conn->prepare("DELETE FROM auth_tokens WHERE token_hash = ?");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
setcookie("remember_token", "", [
    "expires" => time() - 3600,
    "path" => "/",
    "secure" => $isHttps,
    "httponly" => true,
    "samesite" => "Lax"
]);

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();
header("Location: ../Pages/login_user.php");
exit;
?>
