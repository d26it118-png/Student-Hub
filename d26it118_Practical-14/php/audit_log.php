<?php
/**
 * Write a best-effort audit entry for a signed-in administrator.
 * Logging errors are sent to the PHP error log and do not break the main action.
 */
function log_admin_action(mysqli $conn, string $action, string $entity, ?string $entityId = null, ?string $details = null): void
{
    if (($_SESSION["user_role"] ?? "") !== "admin" || empty($_SESSION["user_id"])) {
        return;
    }

    try {
        $userId = (int) $_SESSION["user_id"];
        $userName = (string) ($_SESSION["user_name"] ?? "Admin");
        $ipAddress = substr((string) ($_SERVER["REMOTE_ADDR"] ?? ""), 0, 45);
        $stmt = $conn->prepare(
            "INSERT INTO audit_logs (user_id, user_name, action, entity, entity_id, details, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issssss", $userId, $userName, $action, $entity, $entityId, $details, $ipAddress);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        error_log("StudentHub audit logging failed: " . $e->getMessage());
    }
}
