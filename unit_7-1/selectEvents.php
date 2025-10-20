<?php
require 'dbConnect1.php'; // Include the database connection file

// Use exceptions for errors, but don't expose internals to users.
try {
    $sql = "SELECT events_name, events_description, events_presenter, events_date, events_time FROM wdv341_events";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $loadError = false;
} catch (PDOException $e) {
    // Log the detailed error for admins/developers. This will be used in the final project
    error_log("DB error retrieving events: " . $e->getMessage());

    // Prepare safe defaults for rendering
    $events = [];
    $loadError = true;

    // Optionally, set a user-facing message (don't include $e->getMessage() here in production)
    $userErrorMessage = "Events could not be loaded at this time. Please try again later.";
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
    <div style="margin:0 0 12px 0;"><a class="toplink" href="https://github.com/huntlovan/php_341/tree/main/unit_7-1" target="_blank" rel="noopener">&larr; Select Events github repo</a></div>
<h2>Event Listings</h2>

<?php if (!empty($events)): ?>
    <table class="table" role="table" aria-label="Events">
      <thead>
        <tr>
          <th scope="col">Event Name</th>
          <th scope="col">Description</th>
          <th scope="col">Presenter</th>
          <th scope="col">Date</th>
          <th scope="col">Time</th>
        </tr>
      </thead>
      <tbody>
        <!-- Use a PHP loop to process each row in the result. -->
        <?php foreach ($events as $event): ?>
          <tr>
            <td><?= htmlspecialchars($event['events_name']) ?></td>
            <td><?= htmlspecialchars($event['events_description']) ?></td>
            <td><?= htmlspecialchars($event['events_presenter']) ?></td>
            <td><?= (new DateTime($event['events_date']))->format('F j, Y') ?></td>
            <td><?= htmlspecialchars($event['events_time']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
<?php else: ?>
    <p class="no-events">No events found.</p>
<?php endif; ?>

</div>
</body>
</html>
