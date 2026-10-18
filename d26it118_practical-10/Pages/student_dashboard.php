
<?php
require_once __DIR__ . "/../php/auth_check.php";

// Allow only users with the student role.
require_role("student");

// Prevent browser caching of the dashboard.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard | StudentHub</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main>
        <h1>Student Dashboard</h1>

        <h2>
            Welcome,
            <?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES, "UTF-8") ?>!
        </h2>

        <p>You are signed in as a student.</p>

        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($_SESSION["user_email"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Role:</strong>
            <?= htmlspecialchars($_SESSION["user_role"], ENT_QUOTES, "UTF-8") ?>
        </p>

        <p>
            <strong>Last Login:</strong>
            <?= htmlspecialchars(
                $_SESSION["previous_login"] ?? "Login timestamp saved in database",
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </p>

        <p>
            <a href="../php/logout.php">Logout</a>
        </p>

        <p>
            <a href="index.html">StudentHub Home</a>
        </p>
    </main>

</body>

</html>
