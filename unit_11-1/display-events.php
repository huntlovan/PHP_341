<?php
// display-events.php - Display all events with delete functionality
session_start();

// Fetch all events from the database
$events = [];
$message = '';
$messageType = '';

// Check for success/error messages from redirects
if (isset($_GET['deleted']) && $_GET['deleted'] === 'success') {
    $message = 'Event successfully deleted!';
    $messageType = 'success';
} elseif (isset($_GET['deleted']) && $_GET['deleted'] === 'error') {
    $message = 'Error: Unable to delete event. Please try again.';
    $messageType = 'error';
}

try {
    require_once __DIR__ . '/db-connect1.php';
    
    $sql = "SELECT events_id, events_name, events_description, events_presenter, 
                   events_date, events_time 
            FROM wdv341_events 
            ORDER BY events_date DESC, events_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = 'Error loading events: ' . $e->getMessage();
    $messageType = 'error';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event List with Delete</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f0f2f5;color:#333}
        .container{max-width:1200px;margin:40px auto;padding:0 20px}
        h1{margin:0 0 24px;font-size:32px;font-weight:600;color:#1a1a2e}
        .message{padding:16px 20px;border-radius:10px;margin-bottom:24px;font-weight:500;animation:slideIn .3s ease}
        .message.success{background:#ecfdf5;color:#065f46;border:1px solid #10b981}
        .message.error{background:#fef2f2;color:#991b1b;border:1px solid #dc2626}
        @keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .events-grid{display:grid;gap:20px}
        .event-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,.08);display:grid;grid-template-columns:1fr auto;gap:20px;align-items:start;transition:box-shadow .2s}
        .event-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.12)}
        .event-info h2{margin:0 0 8px;font-size:22px;color:#1a1a2e}
        .event-desc{color:#666;margin:0 0 12px;line-height:1.6}
        .event-meta{display:flex;gap:24px;flex-wrap:wrap;font-size:14px}
        .meta-item{display:flex;align-items:center;gap:6px;color:#555}
        .meta-item strong{color:#4f46e5;font-weight:600}
        .event-actions{display:flex;flex-direction:column;gap:10px}
        .btn-delete{padding:12px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background .2s}
        .btn-delete:hover{background:#b91c1c}
        .empty-state{text-align:center;padding:80px 20px;background:#fff;border-radius:12px;color:#666}
        .empty-state h2{color:#1a1a2e;margin:0 0 12px}
        /* Honeypot - hidden from users */
        .honeypot{position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden}
    </style>
</head>
<body>
    <div class="container">
        <h1>📅 Event List</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <h2>No Events Found</h2>
                <p>There are currently no events in the system.</p>
            </div>
        <?php else: ?>
            <form id="eventsForm">
                <!-- Single honeypot for the entire form -->
                <div class="honeypot" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
                
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <div class="event-info">
                                <h2><?php echo htmlspecialchars($event['events_name']); ?></h2>
                                <p class="event-desc"><?php echo htmlspecialchars($event['events_description']); ?></p>
                                <div class="event-meta">
                                    <div class="meta-item">
                                        <span>👤</span>
                                        <strong><?php echo htmlspecialchars($event['events_presenter']); ?></strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>📆</span>
                                        <?php echo date('M j, Y', strtotime($event['events_date'])); ?>
                                    </div>
                                    <div class="meta-item">
                                        <span>🕐</span>
                                        <?php echo date('g:i A', strtotime($event['events_time'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="event-actions">
                                <button type="button" 
                                        class="btn-delete" 
                                        data-event-id="<?php echo $event['events_id']; ?>"
                                        data-event-name="<?php echo htmlspecialchars($event['events_name']); ?>">
                                    🗑️ Delete Event
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
        // Add event listeners to all delete buttons
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const honeypotField = document.getElementById('website');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-event-id');
                    const eventName = this.getAttribute('data-event-name');
                    
                    // Check honeypot - if filled, reject silently
                    if (honeypotField && honeypotField.value.trim() !== '') {
                        alert('Invalid submission detected.');
                        return;
                    }
                    
                    // Confirmation message
                    const confirmed = confirm(`Are you sure you want to delete the event:\n\n"${eventName}"?\n\nThis action cannot be undone.`);
                    
                    if (confirmed) {
                        // Get honeypot value (should be empty for legitimate users)
                        const honeypotValue = honeypotField ? honeypotField.value : '';
                        
                        // Redirect to delete-event.php with event ID and honeypot
                        window.location.href = `delete-event.php?id=${eventId}&honeypot=${encodeURIComponent(honeypotValue)}`;
                    }
                });
            });
        });
    </script>
</body>
</html>
