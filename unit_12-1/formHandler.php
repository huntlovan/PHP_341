<?php
// formHandler.php - Processes Assignment 4-1 form with honeypot protection
// If a bot fills it, the submission is identified as spam and rejected before it is processed or sent. 
session_start();

$message = '';
$messageType = '';
$formData = [];

// Check if form was submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Honeypot validation - check if the hidden field was filled
    $honeypot = trim($_POST['website'] ?? '');
    
    if ($honeypot !== '') {
        // Bot detected - honeypot was filled
        // Log this attempt or send alert email
        error_log("Honeypot triggered - possible bot submission from IP: " . $_SERVER['REMOTE_ADDR']);
        
        // Silently fail or show generic error
        $message = 'There was an error processing your submission. Please try again.';
        $messageType = 'error';
        
        // Optionally send alert email to admin
        $adminEmail = 'admin@yourdomain.com'; // Change this
        $subject = 'Form Bot Detection Alert';
        $alertMessage = "Honeypot triggered on formHandler.php\n";
        $alertMessage .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $alertMessage .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $alertMessage .= "Honeypot value: " . $honeypot . "\n";
        @mail($adminEmail, $subject, $alertMessage);
        
    } else {
        // Honeypot validation passed - process the form
        
        // Collect and sanitize form data
        $formData = [
            'firstName' => trim($_POST['firstName'] ?? ''),
            'lastName' => trim($_POST['lastName'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'eventInterest' => trim($_POST['eventInterest'] ?? ''),
            'comments' => trim($_POST['comments'] ?? '')
        ];
        
        // Basic validation
        if (empty($formData['firstName']) || empty($formData['lastName']) || empty($formData['email'])) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'error';
        } else {
            // Form is valid - process it
            $messageType = 'success';
            
            // Save submission to file for localhost testing (mail() often doesn't work)
            $submissionsDir = __DIR__ . '/form_submissions';
            if (!is_dir($submissionsDir)) {
                @mkdir($submissionsDir, 0755, true);
            }
            
            $filename = $submissionsDir . '/submission_' . date('Y-m-d_His') . '.txt';
            $fileContent = "=== EVENT SIGNUP FORM SUBMISSION ===\n\n";
            $fileContent .= "Name: " . $formData['firstName'] . " " . $formData['lastName'] . "\n";
            $fileContent .= "Email: " . $formData['email'] . "\n";
            $fileContent .= "Phone: " . ($formData['phone'] ?: 'Not provided') . "\n";
            $fileContent .= "Event Interest: " . ($formData['eventInterest'] ?: 'Not specified') . "\n";
            $fileContent .= "Comments: " . ($formData['comments'] ?: 'None') . "\n\n";
            $fileContent .= "---\n";
            $fileContent .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
            $fileContent .= "IP Address: " . $_SERVER['REMOTE_ADDR'];
            
            @file_put_contents($filename, $fileContent);
            
            // TODO: Final project: save to database, and send email confirmation, etc.
            // For now, we'll just display the success message
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Submission Result</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f0f2f5;color:#333}
        .container{max-width:700px;margin:60px auto;background:#fff;padding:40px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        h1{margin:0 0 20px;font-size:28px;font-weight:600;color:#1a1a2e}
        .message{padding:20px;border-radius:10px;margin-bottom:24px;font-size:18px;line-height:1.6}
        .message.success{background:#ecfdf5;color:#065f46;border:2px solid #10b981}
        .message.error{background:#fef2f2;color:#991b1b;border:2px solid #dc2626}
        .data-display{background:#f9fafb;padding:20px;border-radius:10px;border:1px solid #e5e7eb;margin-top:24px}
        .data-row{display:grid;grid-template-columns:150px 1fr;gap:12px;padding:12px 0;border-bottom:1px solid #e5e7eb}
        .data-row:last-child{border-bottom:none}
        .data-label{font-weight:600;color:#4b5563}
        .data-value{color:#1f2937}
        .btn-back{display:inline-block;margin-top:24px;padding:12px 24px;background:#4f46e5;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
        .btn-back:hover{background:#4338ca}
        .icon{font-size:48px;margin-bottom:16px}
    </style>
</head>
<body>
    <div class="container">
        <?php if ($messageType === 'success'): ?>
            <div class="icon">✅</div>
            <h1>Registration Successful!</h1>
            <div class="message success">
                <strong>Thank you <?php echo htmlspecialchars($formData['firstName']); ?> <?php echo htmlspecialchars($formData['lastName']); ?>!</strong><br><br>
                Your registration has been received and processed successfully.
            </div>
            
            <div class="data-display">
                <h2 style="margin:0 0 16px;font-size:20px;color:#1a1a2e">Registration Details:</h2>
                
                <div class="data-row">
                    <div class="data-label">Name:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['firstName'] . ' ' . $formData['lastName']); ?></div>
                </div>
                
                <div class="data-row">
                    <div class="data-label">Email:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['email']); ?></div>
                </div>
                
                <?php if (!empty($formData['phone'])): ?>
                <div class="data-row">
                    <div class="data-label">Phone:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['phone']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($formData['eventInterest'])): ?>
                <div class="data-row">
                    <div class="data-label">Event Interest:</div>
                    <div class="data-value"><?php echo htmlspecialchars($formData['eventInterest']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($formData['comments'])): ?>
                <div class="data-row">
                    <div class="data-label">Comments:</div>
                    <div class="data-value"><?php echo nl2br(htmlspecialchars($formData['comments'])); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <p style="margin-top:24px;padding:16px;background:#fef3c7;border:1px solid #fbbf24;border-radius:8px;color:#92400e">
                📧 A signup confirmation has been sent to <strong><?php echo htmlspecialchars($formData['email']); ?></strong>. Thank you for your support!
            </p>
            
        <?php elseif ($messageType === 'error'): ?>
            <div class="icon">❌</div>
            <h1>Submission Error</h1>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
            
        <?php else: ?>
            <h1>No Form Data</h1>
            <p>No form submission detected. Please submit the form first.</p>
        <?php endif; ?>
        
        <a href="assignment4-1-form.php" class="btn-back">← Back to Form</a>
    </div>
</body>
</html>
