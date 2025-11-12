<?php
// insertEvent.php - processes eventInputForm.html submissions
// Expected POST fields (matching DB columns): events_name, events_description, events_presenter, events_date, events_time, website(honeypot)
// Use the provided connection file; suppress its echo output if any
ob_start();
require_once __DIR__ . '/dbConnect1.php';
ob_end_clean();

// Basic success/failure messaging variables
$message = '';
$error = '';

// Honeypot check first
if (isset($_POST['website']) && trim($_POST['website']) !== '') {
    // Bot likely filled honeypot; silently fail or present generic message
    $error = 'Invalid submission.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required inputs exist
  $required = ['events_name', 'events_description', 'events_presenter', 'events_date', 'events_time'];
    $missing = array_filter($required, fn($f) => empty($_POST[$f]));
    if ($missing) {
        $error = 'Please fill all required fields: ' . implode(', ', $missing);
    } else {
        try {
            //require_once __DIR__ . '/db-connect1.php';

            // Prepare INSERT statement for wdv341_events table.
            // Assuming table columns: event_name, event_description, event_presenter, event_date, event_time, event_inserted, event_updated
            // Adjust column names if your actual schema differs.
      $sql = "INSERT INTO wdv341_events 
        (events_name, events_description, events_presenter, events_date, events_time, events_date_inserted, events_date_updated) 
        VALUES (:name, :description, :presenter, :date, :time, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':name', trim($_POST['events_name']));
            $stmt->bindValue(':description', trim($_POST['events_description']));
            $stmt->bindValue(':presenter', trim($_POST['events_presenter']));
            $stmt->bindValue(':date', $_POST['events_date']);
            $stmt->bindValue(':time', $_POST['events_time']);

            $stmt->execute();
            $message = 'Event successfully inserted!';
        } catch (Throwable $e) {
            $error = 'Error inserting event: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Insert Event</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f7f7fb;color:#333}
    .wrap{max-width:700px;margin:60px auto;background:#fff;padding:32px;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.08)}
    h1{margin-top:0;font-size:28px}
    .msg{padding:16px;border-radius:10px;margin-bottom:20px;font-weight:600}
    .msg.success{background:#ecfdf5;color:#065f46;border:1px solid #10b981}
    .msg.error{background:#fef2f2;color:#991b1b;border:1px solid #dc2626}
    a.button{display:inline-block;margin-top:10px;padding:10px 16px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
    a.button:hover{background:#4338ca}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Insert Event</h1>
    <?php if($message): ?>
      <div class="msg success"><?php echo htmlspecialchars($message); ?></div>
    <?php elseif($error): ?>
      <div class="msg error"><?php echo htmlspecialchars($error); ?></div>
    <?php else: ?>
      <p>Submit the <a href="eventInputForm.html">Event Input Form</a> to add a new event.</p>
    <?php endif; ?>
    <a class="button" href="eventInputForm.html">Back to Form</a>
  </div>
</body>
</html>