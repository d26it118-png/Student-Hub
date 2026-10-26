<?php
/*
 * StudentHub Practical 13 - Events JSON API
 * GET: list events / fetch one event with ?id=1
 * POST JSON: {"action":"add", ...event fields}
 * POST JSON: {"action":"update","event_id":1, ...event fields}
 * POST JSON: {"action":"delete","event_id":1}
 */
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store");
// Use the same local XAMPP credentials as db/db.php, but keep connection
// failures in JSON format so API clients never receive an HTML/plain-text error.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
$conn = null;
try {
    $conn = new mysqli("localhost", "root", "", "studenthub");
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Practical 13 API connection error: " . $e->getMessage());
    respond(500, ["success"=>false, "message"=>"Database connection failed. Start MySQL and check db/db.php credentials."]);
}
function clean_text($value): string {
    return trim(is_string($value) ? $value : "");
}
function validate_event(array $data): array {
    $event = [
        "title" => clean_text($data["title"] ?? ""),
        "category" => clean_text($data["category"] ?? ""),
        "event_date" => clean_text($data["event_date"] ?? ""),
        "venue" => clean_text($data["venue"] ?? ""),
        "organizer" => clean_text($data["organizer"] ?? ""),
        "status" => clean_text($data["status"] ?? "Upcoming")
    ];
    if ($event["title"] === "" || mb_strlen($event["title"]) > 150) {
        respond(422, ["success"=>false, "message"=>"Title is required (maximum 150 characters)."]);
    }
    if ($event["category"] === "" || mb_strlen($event["category"]) > 50) {
        respond(422, ["success"=>false, "message"=>"Category is required (maximum 50 characters)."]);
    }
    $date = DateTime::createFromFormat("!Y-m-d", $event["event_date"]);
    if (!$date || $date->format("Y-m-d") !== $event["event_date"]) {
        respond(422, ["success"=>false, "message"=>"Enter a valid event date."]);
    }
    if ($event["venue"] === "" || mb_strlen($event["venue"]) > 150) {
        respond(422, ["success"=>false, "message"=>"Venue is required (maximum 150 characters)."]);
    }
    if ($event["organizer"] === "" || mb_strlen($event["organizer"]) > 100) {
        respond(422, ["success"=>false, "message"=>"Organizer is required (maximum 100 characters)."]);
    }
    if (!in_array($event["status"], ["Upcoming", "Completed", "Cancelled"], true)) {
        respond(422, ["success"=>false, "message"=>"Select a valid status."]);
    }
    return $event;
}

try {
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        if (isset($_GET["id"]) && (!$id || $id < 1)) {
            respond(400, ["success"=>false, "message"=>"Invalid event ID."]);
        }
        if ($id) {
            $stmt = $conn->prepare("SELECT event_id, title, category, event_date, venue, organizer, status FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $event = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$event) respond(404, ["success"=>false, "message"=>"Event not found."]);
            respond(200, ["success"=>true, "data"=>$event]);
        }
        $result = $conn->query("SELECT event_id, title, category, event_date, venue, organizer, status FROM events ORDER BY event_id DESC");
        $events = $result->fetch_all(MYSQLI_ASSOC);
        respond(200, ["success"=>true, "data"=>$events]);
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Allow: GET, POST");
        respond(405, ["success"=>false, "message"=>"Method not allowed. Use GET or POST."]);
    }

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        respond(400, ["success"=>false, "message"=>"Request body must contain valid JSON."]);
    }
    $action = clean_text($data["action"] ?? "");

    if ($action === "add") {
        $event = validate_event($data);
        $stmt = $conn->prepare("INSERT INTO events (title, category, event_date, venue, organizer, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $event["title"], $event["category"], $event["event_date"], $event["venue"], $event["organizer"], $event["status"]);
        $stmt->execute();
        $id = $conn->insert_id;
        $stmt->close();
        respond(201, ["success"=>true, "message"=>"Event added successfully.", "event_id"=>$id]);
    }

    $id = filter_var($data["event_id"] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) respond(422, ["success"=>false, "message"=>"A valid event_id is required."]);

    if ($action === "update") {
        $event = validate_event($data);
        $stmt = $conn->prepare("UPDATE events SET title=?, category=?, event_date=?, venue=?, organizer=?, status=? WHERE event_id=?");
        $stmt->bind_param("ssssssi", $event["title"], $event["category"], $event["event_date"], $event["venue"], $event["organizer"], $event["status"], $id);
        $stmt->execute();
        $changed = $stmt->affected_rows;
        $stmt->close();
        $check = $conn->prepare("SELECT event_id FROM events WHERE event_id=?");
        $check->bind_param("i", $id); $check->execute();
        $exists = $check->get_result()->num_rows > 0; $check->close();
        if (!$exists) respond(404, ["success"=>false, "message"=>"Event not found."]);
        respond(200, ["success"=>true, "message"=>"Event updated successfully."]);
    }

    if ($action === "delete") {
        $stmt = $conn->prepare("DELETE FROM events WHERE event_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $changed = $stmt->affected_rows;
        $stmt->close();
        if (!$changed) respond(404, ["success"=>false, "message"=>"Event not found or could not be deleted (it may have registrations)."]);
        respond(200, ["success"=>true, "message"=>"Event deleted successfully."]);
    }
    respond(400, ["success"=>false, "message"=>"Unknown action. Use add, update or delete."]);
} catch (mysqli_sql_exception $e) {
    error_log("Practical 13 events API: " . $e->getMessage());
    respond(500, ["success"=>false, "message"=>"Database operation failed. Check database setup and related records."]);
} catch (Throwable $e) {
    error_log("Practical 13 events API: " . $e->getMessage());
    respond(500, ["success"=>false, "message"=>"Unexpected server error."]);
}
