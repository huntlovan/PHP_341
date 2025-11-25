<?php
// delete-event.php - Handles event deletion with honeypot protection
session_start();

$redirectUrl = 'display-events.php';

// Validate request method and required parameters
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: $redirectUrl?deleted=error");
    exit;
}

$eventId = (int)$_GET['id'];
$honeypot = $_GET['honeypot'] ?? '';

// Honeypot check - if filled, reject (bot detected)
if (trim($honeypot) !== '') {
    // Silently fail or redirect without action
    header("Location: $redirectUrl?deleted=error");
    exit;
}

// Proceed with deletion
try {
    require_once __DIR__ . '/db-connect1.php';
    
    // Prepare DELETE statement
    $sql = "DELETE FROM wdv341_events WHERE events_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Check if any row was affected
    if ($stmt->rowCount() > 0) {
        // Success - redirect with success message
        header("Location: $redirectUrl?deleted=success");
    } else {
        // No rows deleted (event not found)
        header("Location: $redirectUrl?deleted=error");
    }
} catch (Exception $e) {
    // Error during deletion
    error_log("Delete event error: " . $e->getMessage());
    header("Location: $redirectUrl?deleted=error");
}

exit;
?>