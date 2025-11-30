<?php
// updateEvent.php - Update an existing event with CSRF token and honeypot protection
session_start();

require_once __DIR__ . '/dbConnect1.php';
//require 'dbConnect1.php'; // Include the database connection file

$message = '';
$messageType = '';
$event = null;
$eventId = null;

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get event ID from GET parameter (supports 'recid' or 'id')
if (isset($_GET['recid']) || isset($_GET['id'])) {
    $eventId = isset($_GET['recid']) ? $_GET['recid'] : $_GET['id'];
    $eventId = filter_var($eventId, FILTER_VALIDATE_INT);
    
    if ($eventId === false || $eventId <= 0) {
        $message = 'Invalid event ID.';
        $messageType = 'error';
    } else {
        // Fetch the event data
        try {
            $sql = "SELECT events_id, events_name, events_description, events_presenter, 
                           events_date, events_time 
                    FROM wdv341_events 
                    WHERE events_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                $message = 'Event not found.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            error_log("Error fetching event: " . $e->getMessage());
            //$message = 'Error loading event data.';
            $message = 'Error loading event data: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $event) {
    
    // 1. CSRF Token Validation
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $message = 'Invalid security token. Please try again.';
        $messageType = 'error';
        
        // Log potential CSRF attack
        error_log("CSRF token mismatch from IP: " . $_SERVER['REMOTE_ADDR']);
    } else {
        
        // 2. Honeypot Validation
        $honeypot = trim($_POST['website'] ?? '');
        if ($honeypot !== '') {
            // Bot detected
            $message = 'Invalid submission detected.';
            $messageType = 'error';
            error_log("Honeypot triggered on updateEvent from IP: " . $_SERVER['REMOTE_ADDR']);
        } else {
            
            // 3. Collect and validate form data
            $formData = [
                'events_name' => trim($_POST['events_name'] ?? ''),
                'events_description' => trim($_POST['events_description'] ?? ''),
                'events_presenter' => trim($_POST['events_presenter'] ?? ''),
                'events_date' => trim($_POST['events_date'] ?? ''),
                'events_time' => trim($_POST['events_time'] ?? '')
            ];
            
            // Validate required fields
            if (empty($formData['events_name']) || empty($formData['events_description']) || 
                empty($formData['events_presenter']) || empty($formData['events_date']) || 
                empty($formData['events_time'])) {
                $message = 'All fields are required.';
                $messageType = 'error';
            } else {
                
                // 4. Update the database
                try {
                    $sql = "UPDATE wdv341_events 
                            SET events_name = :name, 
                                events_description = :description, 
                                events_presenter = :presenter, 
                                events_date = :date, 
                                events_time = :time,
                                events_date_updated = NOW()
                            WHERE events_id = :id";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(':name', $formData['events_name']);
                    $stmt->bindValue(':description', $formData['events_description']);
                    $stmt->bindValue(':presenter', $formData['events_presenter']);
                    $stmt->bindValue(':date', $formData['events_date']);
                    $stmt->bindValue(':time', $formData['events_time']);
                    $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
                    
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        $message = 'Event updated successfully!';
                        $messageType = 'success';
                        
                        // Refresh event data to show updated values
                        $event = array_merge($event, $formData);
                        $event['events_id'] = $eventId;
                        
                        // Generate new CSRF token after successful update
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    } else {
                        $message = 'No changes were made to the event.';
                        $messageType = 'info';
                    }
                    
                } catch (PDOException $e) {
                    error_log("Error updating event: " . $e->getMessage());
                    $message = 'Error updating event. Please try again.';
                    $messageType = 'error';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Event</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f0f2f5;color:#333}
        .container{max-width:700px;margin:40px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        h1{margin:0 0 8px;font-size:28px;font-weight:600;color:#1a1a2e}
        .breadcrumb{color:#666;font-size:14px;margin-bottom:24px}
        .breadcrumb a{color:#4f46e5;text-decoration:none}
        .breadcrumb a:hover{text-decoration:underline}
        .message{padding:16px 20px;border-radius:10px;margin-bottom:24px;font-weight:500}
        .message.success{background:#ecfdf5;color:#065f46;border:1px solid #10b981}
        .message.error{background:#fef2f2;color:#991b1b;border:1px solid #dc2626}
        .message.info{background:#eff6ff;color:#1e40af;border:1px solid #3b82f6}
        .form-group{margin-bottom:18px}
        label{display:block;font-weight:600;margin-bottom:6px;color:#444}
        .required{color:#dc2626}
        input[type="text"],input[type="date"],input[type="time"],textarea{width:100%;padding:12px 14px;border:1px solid #ddd;border-radius:8px;font-size:16px;box-sizing:border-box;font-family:inherit}
        input:focus,textarea:focus{outline:none;border-color:#4f46e5}
        textarea{min-height:100px;resize:vertical}
        button{width:100%;padding:14px;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;margin-top:8px}
        button:hover{background:#4338ca}
        .btn-secondary{background:#6b7280;margin-top:12px}
        .btn-secondary:hover{background:#4b5563}
        .security-badge{background:#f0fdf4;border:1px solid #86efac;padding:12px 16px;border-radius:8px;margin-top:20px;font-size:13px;color:#166534}
        /* Honeypot - hidden from users */
        .honeypot{position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden}
        .event-id-display{background:#f9fafb;padding:12px;border-radius:8px;margin-bottom:20px;font-size:14px;color:#4b5563}
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Event</h1>
        <div class="breadcrumb">
            <a href="selectEvents.php">← Back to Event List</a>
            <?php if (isset($_SESSION['validUser']) && $_SESSION['validUser'] === true): ?>
                | <a href="login.php">Admin Dashboard</a>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($event): ?>
            <div class="event-id-display">
                <strong>Editing Event ID:</strong> <?php echo htmlspecialchars((string)$event['events_id']); ?>
            </div>
            
            <form method="post" action="">
                <!-- CSRF Token Protection -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <!-- Honeypot Protection (optional but included as requested) -->
                <div class="honeypot" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="events_name">Event Name <span class="required">*</span></label>
                    <input type="text" id="events_name" name="events_name" 
                           value="<?php echo htmlspecialchars($event['events_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="events_description">Description <span class="required">*</span></label>
                    <textarea id="events_description" name="events_description" required><?php echo htmlspecialchars($event['events_description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="events_presenter">Presenter <span class="required">*</span></label>
                    <input type="text" id="events_presenter" name="events_presenter" 
                           value="<?php echo htmlspecialchars($event['events_presenter']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="events_date">Event Date <span class="required">*</span></label>
                    <input type="date" id="events_date" name="events_date" 
                           value="<?php echo htmlspecialchars($event['events_date']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="events_time">Event Time <span class="required">*</span></label>
                    <input type="time" id="events_time" name="events_time" 
                           value="<?php echo htmlspecialchars($event['events_time']); ?>" required>
                </div>
                
                <button type="submit">💾 Update Event</button>
                <a href="selectEvents.php" style="display:block;text-decoration:none">
                    <button type="button" class="btn-secondary">Cancel</button>
                </a>
                
                <div class="security-badge">
                    🔒 This form is protected by CSRF token and honeypot security measures.
                </div>
            </form>
            
        <?php elseif (!$eventId): ?>
            <div class="message error">
                No event ID specified. Please select an event from the <a href="selectEvents.php">event list</a>.
            </div>
            
        <?php endif; ?>
    </div>
</body>
</html>