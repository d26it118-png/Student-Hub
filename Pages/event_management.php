<?php
require_once __DIR__ . "/auth_check.php";
require_role("admin");
require_once __DIR__ . "/../db/db.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
function redirect_status(string $status): void {
    header("Location: event_management.php?status=" . rawurlencode($status));
    exit;
}
$successMessages = [
    "added" => "Event added successfully.",
    "updated" => "Event updated successfully.",
    "deleted" => "Event deleted successfully."
];
$success = $successMessages[(string)($_GET["status"] ?? "")] ?? "";
$error = "";
$editEvent = null;
$form = ["title"=>"", "category"=>"Technical", "event_date"=>"", "venue"=>"", "organizer"=>"", "status"=>"Upcoming"];
$uploadDir = __DIR__ . "/../uploads/event_posters/";
$uploadUrl = "../uploads/event_posters/";
$maxBytes = 2 * 1024 * 1024;
$allowedMimes = ["image/jpeg"=>"jpg", "image/png"=>"png", "image/webp"=>"webp"];

function remove_poster_file(?string $filename, string $uploadDir): void {
    if ($filename && basename($filename) === $filename) {
        $path = $uploadDir . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

/* Handle add, update and delete using POST and prepared statements. */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";
    if (!is_string($token) || !hash_equals($_SESSION["csrf_token"], $token)) {
        http_response_code(403);
        exit("Invalid request token. Refresh the page and try again.");
    }

    $action = (string)($_POST["action"] ?? "");
    $eventId = filter_var($_POST["event_id"] ?? null, FILTER_VALIDATE_INT);

    if ($action === "delete") {
        if (!$eventId || $eventId < 1) {
            $error = "Invalid event ID.";
        } else {
            try {
                $stmt = $conn->prepare("SELECT poster FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $old = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $changed = $stmt->affected_rows;
                $stmt->close();

                if ($changed > 0) {
                    if ($old) remove_poster_file($old["poster"] ?? null, $uploadDir);
                    redirect_status("deleted");
                }
                $error = "Event was not deleted; it may no longer exist.";
            } catch (mysqli_sql_exception $e) {
                $error = "Unable to delete the event. Please check related records and try again.";
            }
        }
    } elseif ($action === "add" || $action === "update") {
        $form = [
            "title" => trim((string)($_POST["title"] ?? "")),
            "category" => trim((string)($_POST["category"] ?? "")),
            "event_date" => trim((string)($_POST["event_date"] ?? "")),
            "venue" => trim((string)($_POST["venue"] ?? "")),
            "organizer" => trim((string)($_POST["organizer"] ?? "")),
            "status" => trim((string)($_POST["status"] ?? "Upcoming"))
        ];

        $validDate = DateTime::createFromFormat("Y-m-d", $form["event_date"]);
        if ($form["title"] === "" || mb_strlen($form["title"]) > 150) {
            $error = "Enter an event title (maximum 150 characters).";
        } elseif ($form["category"] === "" || mb_strlen($form["category"]) > 50) {
            $error = "Enter a category (maximum 50 characters).";
        } elseif (!$validDate || $validDate->format("Y-m-d") !== $form["event_date"]) {
            $error = "Select a valid event date.";
        } elseif ($form["venue"] === "" || mb_strlen($form["venue"]) > 150) {
            $error = "Enter a venue (maximum 150 characters).";
        } elseif ($form["organizer"] === "" || mb_strlen($form["organizer"]) > 100) {
            $error = "Enter an organizer (maximum 100 characters).";
        } elseif (!in_array($form["status"], ["Upcoming", "Completed", "Cancelled"], true)) {
            $error = "Select a valid event status.";
        } else {
            $newPoster = null;
            $upload = $_FILES["poster"] ?? null;
            $hasUpload = $upload && ($upload["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

            if ($hasUpload) {
                if ($upload["error"] !== UPLOAD_ERR_OK) {
                    $error = "Poster upload failed. Choose the file again.";
                } elseif ($upload["size"] <= 0 || $upload["size"] > $maxBytes) {
                    $error = "Poster must be smaller than or equal to 2 MB.";
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($upload["tmp_name"]);
                    $imageInfo = @getimagesize($upload["tmp_name"]);
                    if (!isset($allowedMimes[$mime]) || $imageInfo === false) {
                        $error = "Invalid poster. Upload only JPG, PNG, or WEBP image files.";
                    } else {
                        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                            $error = "Poster upload directory could not be created.";
                        } else {
                            $newPoster = bin2hex(random_bytes(16)) . "." . $allowedMimes[$mime];
                            if (!move_uploaded_file($upload["tmp_name"], $uploadDir . $newPoster)) {
                                $newPoster = null;
                                $error = "Could not save the poster. Check folder permissions.";
                            }
                        }
                    }
                }
            }

            if ($error === "") {
                try {
                    if ($action === "add") {
                        $stmt = $conn->prepare("INSERT INTO events (title, category, event_date, venue, organizer, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssss", $form["title"], $form["category"], $form["event_date"], $form["venue"], $form["organizer"], $newPoster, $form["status"]);
                        $stmt->execute();
                        $stmt->close();
                        redirect_status("added");
                    } else {
                        if (!$eventId || $eventId < 1) {
                            $error = "Invalid event ID.";
                        } else {
                            $stmt = $conn->prepare("SELECT poster FROM events WHERE event_id = ?");
                            $stmt->bind_param("i", $eventId);
                            $stmt->execute();
                            $old = $stmt->get_result()->fetch_assoc();
                            $stmt->close();
                            if (!$old) {
                                $error = "Event not found.";
                            } else {
                                if ($newPoster !== null) {
                                    $stmt = $conn->prepare("UPDATE events SET title=?, category=?, event_date=?, venue=?, organizer=?, poster=?, status=? WHERE event_id=?");
                                    $stmt->bind_param("sssssssi", $form["title"], $form["category"], $form["event_date"], $form["venue"], $form["organizer"], $newPoster, $form["status"], $eventId);
                                } else {
                                    $stmt = $conn->prepare("UPDATE events SET title=?, category=?, event_date=?, venue=?, organizer=?, status=? WHERE event_id=?");
                                    $stmt->bind_param("ssssssi", $form["title"], $form["category"], $form["event_date"], $form["venue"], $form["organizer"], $form["status"], $eventId);
                                }
                                $stmt->execute();
                                $stmt->close();
                                if ($newPoster !== null) remove_poster_file($old["poster"] ?? null, $uploadDir);
                                redirect_status("updated");
                            }
                        }
                    }
                } catch (mysqli_sql_exception $e) {
                    if ($newPoster !== null) remove_poster_file($newPoster, $uploadDir);
                    $error = "Database operation failed. Check the event data and database schema.";
                }
            } elseif ($newPoster !== null) {
                remove_poster_file($newPoster, $uploadDir);
            }
        }
    }
}

/* Load record when Edit is selected. */
if (isset($_GET["edit"])) {
    $editId = filter_var($_GET["edit"], FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $stmt = $conn->prepare("SELECT event_id, title, category, event_date, venue, organizer, poster, status FROM events WHERE event_id = ?");
        $stmt->bind_param("i", $editId);
        $stmt->execute();
        $editEvent = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($editEvent) {
            $form = [
                "title" => $editEvent["title"], "category" => $editEvent["category"],
                "event_date" => $editEvent["event_date"], "venue" => $editEvent["venue"],
                "organizer" => $editEvent["organizer"], "status" => $editEvent["status"] ?? "Upcoming"
            ];
        } else {
            $error = "The selected event was not found.";
        }
    }
}

$events = [];
try {
    $result = $conn->query("SELECT event_id, title, category, event_date, venue, organizer, poster, status FROM events ORDER BY event_date DESC, event_id DESC");
    while ($row = $result->fetch_assoc()) $events[] = $row;
} catch (mysqli_sql_exception $e) {
    $error = "Could not load events. Import db/practical12_event_management.sql first.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Management | StudentHub</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.wrap{max-width:1150px;margin:28px auto;padding:20px}.panel{background:#fff;border:1px solid #ddd;border-radius:10px;padding:20px;margin:18px 0}.event-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}.event-form label{display:block;font-weight:600}.event-form input,.event-form select{box-sizing:border-box;width:100%;padding:10px;margin-top:6px}.btn{display:inline-block;padding:8px 12px;border:0;border-radius:5px;background:#174ea6;color:white;text-decoration:none;cursor:pointer}.danger{background:#b3261e}.muted{color:#555}.msg{padding:12px;border-radius:6px;margin:12px 0}.success{background:#e6f4ea;color:#137333}.error{background:#fce8e6;color:#a50e0e}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse}th,td{text-align:left;padding:10px;border-bottom:1px solid #ddd;vertical-align:middle}th{background:#f5f7fa}td img{max-width:100px;max-height:75px;object-fit:cover}.actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}.actions form{margin:0}@media(max-width:600px){.wrap{padding:10px}.panel{padding:12px}}
</style>
</head>
<body>
<nav><a href="admin_dashboard.php">Admin Dashboard</a> <a href="../php/logout.php">Logout</a></nav>
<main class="wrap">
<h1>Event Management</h1>
<p class="muted">Admin-only event CRUD. Poster formats: JPG, PNG, WEBP. Maximum size: 2 MB.</p>
<?php if ($success): ?><div class="msg success"><?= h($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="msg error"><?= h($error) ?></div><?php endif; ?>

<section class="panel">
<h2><?= $editEvent ? "Update Event" : "Add Event" ?></h2>
<form method="post" enctype="multipart/form-data" class="event-form">
<input type="hidden" name="csrf_token" value="<?= h($_SESSION["csrf_token"]) ?>">
<input type="hidden" name="action" value="<?= $editEvent ? "update" : "add" ?>">
<?php if ($editEvent): ?><input type="hidden" name="event_id" value="<?= (int)$editEvent["event_id"] ?>"><?php endif; ?>
<div><label>Event Title<input name="title" maxlength="150" required value="<?= h($form["title"]) ?>"></label></div>
<div><label>Category<input name="category" maxlength="50" required value="<?= h($form["category"]) ?>"></label></div>
<div><label>Event Date<input type="date" name="event_date" required value="<?= h($form["event_date"]) ?>"></label></div>
<div><label>Venue<input name="venue" maxlength="150" required value="<?= h($form["venue"]) ?>"></label></div>
<div><label>Organizer<input name="organizer" maxlength="100" required value="<?= h($form["organizer"]) ?>"></label></div>
<div><label>Status<select name="status"><option <?= $form["status"]==="Upcoming"?"selected":"" ?>>Upcoming</option><option <?= $form["status"]==="Completed"?"selected":"" ?>>Completed</option><option <?= $form["status"]==="Cancelled"?"selected":"" ?>>Cancelled</option></select></label></div>
<div><label>Poster (JPG/PNG/WEBP, max 2 MB)<input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
<?php if ($editEvent && !empty($editEvent["poster"])): ?><p>Current poster: <img src="<?= h($uploadUrl . rawurlencode($editEvent["poster"])) ?>" alt="Current event poster"></p><?php endif; ?>
</div>
<div style="align-self:end"><button class="btn" type="submit"><?= $editEvent ? "Save Changes" : "Add Event" ?></button> <?php if ($editEvent): ?><a class="btn" href="event_management.php">Cancel Edit</a><?php endif; ?></div>
</form>
</section>

<section class="panel">
<h2>Events List (<?= count($events) ?>)</h2>
<div class="table-wrap"><table>
<thead><tr><th>ID</th><th>Poster</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>Organizer</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php if (!$events): ?><tr><td colspan="9">No events found. Add an event above.</td></tr><?php endif; ?>
<?php foreach ($events as $event): ?>
<tr>
<td><?= (int)$event["event_id"] ?></td>
<td><?php if (!empty($event["poster"])): ?><img src="<?= h($uploadUrl . rawurlencode($event["poster"])) ?>" alt="Poster for <?= h($event["title"]) ?>"><?php else: ?>—<?php endif; ?></td>
<td><?= h($event["title"]) ?></td><td><?= h($event["category"]) ?></td><td><?= h($event["event_date"]) ?></td><td><?= h($event["venue"]) ?></td><td><?= h($event["organizer"]) ?></td><td><?= h($event["status"] ?? "Upcoming") ?></td>
<td><div class="actions"><a class="btn" href="event_management.php?edit=<?= (int)$event["event_id"] ?>">Edit</a>
<form method="post" onsubmit="return confirm('Delete this event and its poster?');"><input type="hidden" name="csrf_token" value="<?= h($_SESSION["csrf_token"]) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="event_id" value="<?= (int)$event["event_id"] ?>"><button class="btn danger" type="submit">Delete</button></form></div></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</section>
<p><a href="admin_dashboard.php">← Back to Admin Dashboard</a></p>
</main>
</body>
</html>
