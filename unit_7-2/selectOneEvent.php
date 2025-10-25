<?php
require 'dbConnect1.php'; // Include the database connection file

// For testing purposes hard code your event number into the variable. 
// Hardcode an event number per the assignment (allow optional ?id= override for convenience)
$eventId = 1;
if (isset($_GET['id'])) {
  $tmp = filter_var($_GET['id'], FILTER_VALIDATE_INT);
  if ($tmp !== false && $tmp > 0) {
    $eventId = $tmp;
  }
}

$event = null;
$loadError = false;

try {
  // Use prepared statement with WHERE to select a single event
  $sql = "SELECT events_id, events_name, events_description, events_presenter, events_date, events_time 
      FROM wdv341_events 
      WHERE events_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
  $stmt->execute();
  $event = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
  error_log("DB error retrieving single event: " . $e->getMessage());
  $loadError = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDV341 Event Listings</title>
    <style>
        :root{
          --blue-50:  #eff6ff;
          --blue-100: #dbeafe;
          --blue-200: #bfdbfe;
          --blue-600: #2563eb;
          --blue-700: #1d4ed8;
          --text:     #0f172a;
          --muted:    #475569;
          --border:   #e2e8f0;
        }
        body{ font-family: Arial, Helvetica, sans-serif; margin: 0; background:#ffffff; color: var(--text); }
        .container{ padding: 20px; }

        a.toplink{ color: var(--blue-700); text-decoration: none; font-weight: 600; }
        a.toplink:hover{ text-decoration: underline; }

        .table{ width:100%; border-collapse: collapse; border:1px solid var(--blue-200); background:#fff; }
        .table thead th{ background: var(--blue-600); color:#fff; padding:12px; text-align:left; position: sticky; top: 0; }
        .table tbody td{ padding:12px; border-top:1px solid var(--blue-200); }
        /* Blue & white zebra striping */
        .table tbody tr:nth-child(odd){ background:#ffffff; }
        .table tbody tr:nth-child(even){ background: var(--blue-50); }
        .table tbody tr:hover{ background: var(--blue-100); }

        h2{ margin: 8px 0 16px; color: var(--blue-700); }
        .no-events{ text-align:center; font-size:18px; color: var(--muted); padding:16px; background: var(--blue-50); border:1px dashed var(--blue-200); border-radius:8px; }
    </style>
</head>
<body>

<div class="container">
  <!-- Top link to return to the course home page -->
    <div style="margin:0 0 12px 0;"><a class="toplink" href="http://kickshunter.com/WDV341/wdv341.php" target="_blank" rel="noopener">&larr; Back to WDV341 Home</a></div>
    <div style="margin:0 0 12px 0;"><a class="toplink" href="https://github.com/huntlovan/php_341/tree/main/unit_7-2" target="_blank" rel="noopener">&larr; Select Events github repo</a></div>
<h2>Event Details</h2>

<?php if (!$loadError && $event): ?>
    <table class="table" role="table" aria-label="One Event">
      <thead>
        <tr>
          <th scope="col">Field</th>
          <th scope="col">Value</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Event ID</td>
          <td><?= htmlspecialchars((string)($event['events_id'] ?? $eventId)) ?></td>
        </tr>
        <tr>
          <td>Event Name</td>
          <td><?= htmlspecialchars((string)($event['events_name'] ?? '')) ?></td>
        </tr>
        <tr>
          <td>Description</td>
          <td><?= htmlspecialchars((string)($event['events_description'] ?? '')) ?></td>
        </tr>
        <tr>
          <td>Presenter</td>
          <td><?= htmlspecialchars((string)($event['events_presenter'] ?? '')) ?></td>
        </tr>
        <tr>
          <td>Date</td>
          <td><?php 
            $d = $event['events_date'] ?? '';
            echo $d ? (new DateTime($d))->format('F j, Y') : '';
          ?></td>
        </tr>
        <tr>
          <td>Time</td>
          <td><?= htmlspecialchars((string)($event['events_time'] ?? '')) ?></td>
        </tr>
      </tbody>
    </table>
<?php else: ?>
    <p class="no-events">No event found for ID <?= htmlspecialchars((string)$eventId) ?>.</p>
<?php endif; ?>

</div>
</body>
</html>
