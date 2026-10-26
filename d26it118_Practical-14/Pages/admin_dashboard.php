<?php
require_once __DIR__ . "/../php/auth_check.php";
require_role("admin");
require_once __DIR__ . "/../php/audit_log.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

log_admin_action($conn, "view_dashboard", "dashboard", null, "Administrator opened the admin dashboard");

$stats = ["users" => 0, "students" => 0, "events" => 0, "registrations" => 0];
$statQueries = [
    "users" => "SELECT COUNT(*) AS total FROM users",
    "students" => "SELECT COUNT(*) AS total FROM students",
    "events" => "SELECT COUNT(*) AS total FROM events",
    "registrations" => "SELECT COUNT(*) AS total FROM registrations"
];
foreach ($statQueries as $key => $sql) {
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats[$key] = (int)($row["total"] ?? 0);
        $result->free();
    }
}

$latestRegistrations = [];
$registrationSql = "SELECT r.registration_id, r.student_id, s.full_name, e.title AS event_title, r.registration_date
                    FROM registrations r
                    LEFT JOIN students s ON s.student_id = r.student_id
                    LEFT JOIN events e ON e.event_id = r.event_id
                    ORDER BY r.registration_date DESC, r.registration_id DESC
                    LIMIT 8";
if ($result = $conn->query($registrationSql)) {
    while ($row = $result->fetch_assoc()) $latestRegistrations[] = $row;
    $result->free();
}

$auditEntries = [];
if ($result = $conn->query("SELECT user_name, action, entity, entity_id, details, created_at FROM audit_logs ORDER BY log_id DESC LIMIT 10")) {
    while ($row = $result->fetch_assoc()) $auditEntries[] = $row;
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | StudentHub - Practical 14</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
      .p14-wrap{max-width:1200px;margin:24px auto;padding:20px}
      .p14-nav{display:flex;gap:12px;flex-wrap:wrap;padding:14px;background:#17365d;border-radius:10px}
      .p14-nav a{color:#fff;text-decoration:none;padding:8px 10px;border-radius:5px}
      .p14-nav a:hover{background:#2d5485}
      .p14-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin:22px 0}
      .p14-card{background:#fff;border:1px solid #dce2e9;border-radius:12px;padding:20px;box-shadow:0 3px 12px #0000000a}
      .p14-number{font-size:2rem;font-weight:700;margin:8px 0;color:#17365d}
      .p14-table-wrap{overflow-x:auto} .p14-table{width:100%;border-collapse:collapse}
      .p14-table th,.p14-table td{padding:10px;border-bottom:1px solid #e4e7eb;text-align:left}
      .p14-table th{background:#f2f5f9}
      .p14-muted{color:#5c6675;font-size:.92rem}
    </style>
</head>
<body>
<main class="p14-wrap">
    <h1>StudentHub Admin Dashboard</h1>
    <p>Welcome, <strong><?= e($_SESSION["user_name"] ?? "Admin") ?></strong>. This dashboard displays live database statistics.</p>
    <p><strong>Signed-in role:</strong> <?= e($_SESSION["user_role"] ?? "") ?> |
       <strong>Email:</strong> <?= e($_SESSION["user_email"] ?? "") ?></p>

    <nav class="p14-nav" aria-label="Administrator menu">
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="student_management.php">Manage Students</a>
      <a href="event_management.php">Manage Events</a>
      <a href="events_ajax.php">Fetch API CRUD</a>
      <a href="index.html">StudentHub Home</a>
      <a href="../php/logout.php">Logout</a>
    </nav>

    <section class="p14-grid" aria-label="Dashboard statistics">
      <article class="p14-card"><div class="p14-muted">Total Users</div><div class="p14-number"><?= $stats["users"] ?></div><div class="p14-muted">Accounts in users table</div></article>
      <article class="p14-card"><div class="p14-muted">Total Students</div><div class="p14-number"><?= $stats["students"] ?></div><div class="p14-muted">Records in students table</div></article>
      <article class="p14-card"><div class="p14-muted">Total Events</div><div class="p14-number"><?= $stats["events"] ?></div><div class="p14-muted">Records in events table</div></article>
      <article class="p14-card"><div class="p14-muted">Registrations</div><div class="p14-number"><?= $stats["registrations"] ?></div><div class="p14-muted">Event registrations recorded</div></article>
    </section>

    <section class="p14-card">
      <h2>Latest Registrations</h2>
      <?php if (!$latestRegistrations): ?>
        <p>No registrations have been recorded yet.</p>
      <?php else: ?>
      <div class="p14-table-wrap"><table class="p14-table">
        <thead><tr><th>Registration ID</th><th>Student ID</th><th>Student Name</th><th>Event</th><th>Registration Date</th></tr></thead>
        <tbody><?php foreach ($latestRegistrations as $r): ?>
          <tr>
            <td><?= e($r["registration_id"]) ?></td>
            <td><?= e($r["student_id"]) ?></td>
            <td><?= e($r["full_name"] ?? "Student record unavailable") ?></td>
            <td><?= e($r["event_title"] ?? "Event record unavailable") ?></td>
            <td><?= e($r["registration_date"]) ?></td>
          </tr>
        <?php endforeach; ?></tbody>
      </table></div>
      <?php endif; ?>
    </section>

    <section class="p14-card" style="margin-top:20px">
      <h2>Recent Admin Audit Log</h2>
      <p class="p14-muted">Records important administrator activity for traceability.</p>
      <?php if (!$auditEntries): ?>
        <p>No audit entries found yet. Refresh this dashboard after creating the audit_logs table.</p>
      <?php else: ?>
      <div class="p14-table-wrap"><table class="p14-table">
        <thead><tr><th>Date / Time</th><th>Admin</th><th>Action</th><th>Entity</th><th>Entity ID</th><th>Details</th></tr></thead>
        <tbody><?php foreach ($auditEntries as $log): ?>
          <tr>
            <td><?= e($log["created_at"]) ?></td><td><?= e($log["user_name"]) ?></td>
            <td><?= e($log["action"]) ?></td><td><?= e($log["entity"]) ?></td>
            <td><?= e($log["entity_id"] ?? "-") ?></td><td><?= e($log["details"] ?? "") ?></td>
          </tr>
        <?php endforeach; ?></tbody>
      </table></div>
      <?php endif; ?>
    </section>
    <p class="p14-muted">Statistics are calculated from MySQL when this page loads. Access is restricted to users with the admin role.</p>
</main>
</body>
</html>
