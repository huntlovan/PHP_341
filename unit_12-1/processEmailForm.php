<?php
// processEmailForm.php - Processes contact form with reCAPTCHA and honeypot protection
session_start();

// Configuration
$recaptchaSecretKey = 'actual-secrete-key-has-been-removed-yeah'; // Got from Google reCAPTCHA admin
$adminEmail = 'hunter.lovan36@gmail.com'; // using my email for now
$supportEmail = 'hunter.lovan36@gmail.com'; // using my email for now

$message = '';
$messageType = '';
$formData = [];
$securityFailed = false;

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // First check: Honeypot validation
    $honeypot = trim($_POST['website_url'] ?? '');
    
    if ($honeypot !== '') {
        // Bot detected - honeypot was filled
        $securityFailed = true;
        error_log("Honeypot triggered - Bot detected from IP: " . $_SERVER['REMOTE_ADDR']);
        
        // Send alert email to support
        $alertSubject = "🚨 Form Bot Detection Alert";
        $alertBody = "Security Alert: Honeypot Protection Triggered\n\n";
        $alertBody .= "Form: Contact Form (processEmailForm.php)\n";
        $alertBody .= "Date/Time: " . date('Y-m-d H:i:s') . "\n";
        $alertBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $alertBody .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
        $alertBody .= "Honeypot Value: " . $honeypot . "\n";
        $alertBody .= "\nThis was likely a bot attempting to submit the form.";
        
        $headers = "From: hunter.lovan36@gmail.com\r\n";
        @mail($supportEmail, $alertSubject, $alertBody, $headers);
        
        // Show generic error to user (we don't reveal security mechanism)
        $message = 'There was an error processing your submission. Please try again later.';
        $messageType = 'error';
    }
    //Part 2. For the Project Email Form do the following
    // Second check: reCAPTCHA validation (if honeypot passed)
    if (!$securityFailed) {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        
        if (empty($recaptchaResponse)) {
            // No reCAPTCHA response
            $securityFailed = true;
            $message = 'Please complete the reCAPTCHA verification.';
            $messageType = 'error';
            
            // Log this attempt
            error_log("reCAPTCHA missing from IP: " . $_SERVER['REMOTE_ADDR']);
        } else {
            // Verify reCAPTCHA with Google
            $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
            $verifyData = [
                'secret' => $recaptchaSecretKey,
                'response' => $recaptchaResponse,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ];
            
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($verifyData)
                ]
            ];
            
            $context = stream_context_create($options);
            $verifyResponse = @file_get_contents($verifyURL, false, $context);
            $responseData = json_decode($verifyResponse);
            
            if (!$responseData || !$responseData->success) {
                // reCAPTCHA validation failed
                $securityFailed = true;
                $message = 'reCAPTCHA verification failed. Please try again.';
                $messageType = 'error';
                
                // Send alert email
                $alertSubject = "🚨 Form Security Alert - reCAPTCHA Failed";
                $alertBody = "Security Alert: reCAPTCHA Verification Failed\n\n";
                $alertBody .= "Form: Contact Form (processEmailForm.php)\n";
                $alertBody .= "Date/Time: " . date('Y-m-d H:i:s') . "\n";
                $alertBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
                $alertBody .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
                
                if ($responseData) {
                    $alertBody .= "Error Codes: " . implode(', ', $responseData->{'error-codes'} ?? []) . "\n";
                }
                
                $headers = "From: security@yourdomain.com\r\n";
                @mail($supportEmail, $alertSubject, $alertBody, $headers);
                
                error_log("reCAPTCHA failed from IP: " . $_SERVER['REMOTE_ADDR']);
            }
        }
    }
    
    // If security checks passed, process the form
    // TODO: Final project: successfully process the form as directed in the project.
    if (!$securityFailed) {
        // Collect and sanitize form data
        $formData = [
            'fullName' => trim($_POST['fullName'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'message' => trim($_POST['message'] ?? '')
        ];
        
        // Validate required fields
        if (empty($formData['fullName']) || empty($formData['email']) || 
            empty($formData['subject']) || empty($formData['message'])) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'error';
        } else {
            // Form is valid - send email
            $to = $adminEmail;
            $emailSubject = "Contact Form Submission: " . $formData['subject'];
            
            $emailBody = "New Contact Form Submission\n\n";
            $emailBody .= "Name: " . $formData['fullName'] . "\n";
            $emailBody .= "Email: " . $formData['email'] . "\n";
            $emailBody .= "Phone: " . ($formData['phone'] ?: 'Not provided') . "\n";
            $emailBody .= "Subject: " . $formData['subject'] . "\n\n";
            $emailBody .= "Message:\n" . $formData['message'] . "\n\n";
            $emailBody .= "---\n";
            $emailBody .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
            $emailBody .= "IP Address: " . $_SERVER['REMOTE_ADDR'];
            
            $headers = "From: " . $formData['email'] . "\r\n";
            $headers .= "Reply-To: " . $formData['email'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // If the form was not submitted successfull send an email to you/support/host 
            // person indicating that an attempt was made to access your form.
            // Try to send email - mail() often doesn't work on localhost
            $emailSent = @mail($to, $emailSubject, $emailBody, $headers);
            
            // LOCALHOST FALLBACK: Save to file if mail() fails (common on localhost)
            // TODO: test locally
            if (!$emailSent) {
                // Create submissions directory if it doesn't exist
                $submissionsDir = __DIR__ . '/email_submissions';
                if (!is_dir($submissionsDir)) {
                    @mkdir($submissionsDir, 0755, true);
                }
                
                // Save submission to a text file with timestamp
                $filename = $submissionsDir . '/submission_' . date('Y-m-d_His') . '.txt';
                $fileContent = "=== CONTACT FORM SUBMISSION ===\n\n";
                $fileContent .= $emailBody;
                
                if (@file_put_contents($filename, $fileContent)) {
                    $message = 'Your message has been received successfully!';
                    $messageType = 'success';
                    // Log for debugging
                    error_log("Email saved to file (mail() unavailable): $filename");
                } else {
                    $message = 'Sorry, there was an error processing your message. Please try again later.';
                    $messageType = 'error';
                    error_log("Failed to save email submission to file: $filename");
                }
            } else {
                $message = 'Your message has been sent successfully!';
                $messageType = 'success';
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
    <title>Contact Form - Result</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
        .container{max-width:600px;background:#fff;padding:48px;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3);text-align:center}
        .icon{font-size:64px;margin-bottom:20px}
        h1{margin:0 0 16px;font-size:32px;font-weight:700;color:#1a1a2e}
        .message{padding:24px;border-radius:12px;margin:24px 0;font-size:18px;line-height:1.6}
        .message.success{background:#ecfdf5;color:#065f46;border:2px solid #10b981}
        .message.error{background:#fef2f2;color:#991b1b;border:2px solid #dc2626}
        .details{background:#f9fafb;padding:20px;border-radius:10px;margin-top:24px;text-align:left}
        .detail-row{padding:12px 0;border-bottom:1px solid #e5e7eb}
        .detail-row:last-child{border-bottom:none}
        .detail-label{font-weight:600;color:#4b5563;margin-bottom:4px}
        .detail-value{color:#1f2937}
        .btn{display:inline-block;margin-top:24px;padding:14px 32px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;text-decoration:none;border-radius:10px;font-weight:700;transition:transform .2s}
        .btn:hover{transform:translateY(-2px)}
    </style>
</head>
<body>
    <div class="container">
        <?php if ($messageType === 'success'): ?>
            <div class="icon">✅</div>
            <h1>Message Sent!</h1>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
            
            <div class="details">
                <p style="margin:0 0 16px;font-weight:600;color:#1a1a2e">Your Submission:</p>
                
                <div class="detail-row">
                    <div class="detail-label">Name:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($formData['fullName']); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($formData['email']); ?></div>
                </div>
                
                <?php if (!empty($formData['phone'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Phone:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($formData['phone']); ?></div>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <div class="detail-label">Subject:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($formData['subject']); ?></div>
                </div>
            </div>
            
            <p style="margin-top:24px;color:#6b7280">We'll get back to you as soon as possible!</p>
            
        <?php elseif ($messageType === 'error'): ?>
            <div class="icon">❌</div>
            <h1>Submission Error</h1>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
            
        <?php else: ?>
            <div class="icon">📭</div>
            <h1>No Form Data</h1>
            <p>Please submit the contact form first.</p>
        <?php endif; ?>
        
        <a href="contactForm.php" class="btn">← Back to Contact Form</a>
    </div>
</body>
</html>
