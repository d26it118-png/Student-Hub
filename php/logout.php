```php
<?php
$isHttps = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";

session_start();

require_once __DIR__ . "/../db/db_connect_mysqli.php";

// Remove remember-me token only if the table and cookie are available.
if (!empty($_COOKIE["remember_token"])) {
    $tokenHash = hash("sha256", $_COOKIE["remember_token"]);

    $stmt = $conn->prepare(
        "DELETE FROM auth_tokens WHERE token_hash = ?"
    );

    if ($stmt) {
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// Clear remember-me cookie.
setcookie("remember_token", "", [
    "expires" => time() - 3600,
    "path" => "/",
    "secure" => $isHttps,
    "httponly" => true,
    "samesite" => "Lax"
]);

// Clear session data and destroy the session.
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

session_destroy();

header("Location: ../Pages/login_user.php");
exit;
?>