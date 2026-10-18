
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"], $_SESSION["user_role"])) {
    header("Location: ../Pages/login_user.php?error=login_required");
    exit;
}

$timeoutSeconds = 900;

if (
    !isset($_SESSION["last_activity"]) ||
    (time() - (int) $_SESSION["last_activity"]) > $timeoutSeconds
) {
    $_SESSION = [];
    session_destroy();

    header("Location: ../Pages/login_user.php?error=timeout");
    exit;
}

$_SESSION["last_activity"] = time();

function require_role(string $role): void
{
    if (($_SESSION["user_role"] ?? "") !== $role) {
        if (($_SESSION["user_role"] ?? "") === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    }
}
