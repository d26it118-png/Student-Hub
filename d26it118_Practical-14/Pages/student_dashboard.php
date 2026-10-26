<?php
require_once __DIR__ . "/../php/auth_check.php";
require_role("student");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard | StudentHub</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .p14-wrap{max-width:1000px;margin:24px auto;padding:20px}
    .p14-nav{display:flex;gap:10px;flex-wrap:wrap;background:#17365d;padding:14px;border-radius:10px}
    .p14-nav a{color:#fff;text-decoration:none;padding:8px 10px;border-radius:5px}
    .p14-nav a:hover{background:#2d5485}
    .p14-card{background:#fff;border:1px solid #dce2e9;border-radius:12px;padding:20px;margin-top:20px}
  </style>
</head>
<body>
<main class="p14-wrap">
  <h1>Student Dashboard</h1>
  <h2>Welcome, <?= e($_SESSION["user_name"] ?? "Student") ?>!</h2>
  <p>You are signed in with the <strong><?= e($_SESSION["user_role"] ?? "student") ?></strong> role.</p>
  <nav class="p14-nav" aria-label="Student menu">
    <a href="student_dashboard.php">Dashboard</a>
    <a href="events.php">View Events</a>
    <a href="event_register.php">Register for Event</a>
    <a href="profile.html">Profile</a>
    <a href="index.html">StudentHub Home</a>
    <a href="../php/logout.php">Logout</a>
  </nav>
  <section class="p14-card">
    <h2>Your Account</h2>
    <p><strong>Email:</strong> <?= e($_SESSION["user_email"] ?? "") ?></p>
    <p><strong>Role:</strong> <?= e($_SESSION["user_role"] ?? "student") ?></p>
    <p class="muted">Student accounts do not see administrator-only options such as Manage Students, Manage Events, or the Admin Dashboard.</p>
  </section>
</main>
</body>
</html>
