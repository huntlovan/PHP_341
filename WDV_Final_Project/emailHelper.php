<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * emailHelper.php - Wrapper class for PHPMailer with fallback support
 * This class provides a simple interface for sending emails using PHPMailer
 * with automatic fallback to file-based logging if email fails or PHPMailer is unavailable.
 * Dependencies: called from contactController.php (work in progress) and depends on PHPMailer
 * ToDo: process fine, but email is not going out to valid email address.Ugh!
 */

class emailHelper {
    private $config;
    private $mailer = null;
    private $lastError = '';
    private $phpMailerAvailable = false;
    
    /**
     * Constructor - Initialize with configuration
     */
    public function __construct($configFile = 'email_config.php') {
        // Load configuration
        if (!file_exists($configFile)) {
            $this->lastError = "Email configuration file not found: $configFile";
            $this->config = $this->getDefaultConfig();
            return; // Continue without throwing exception
        }
        $this->config = require $configFile;
        
        // Try to load PHPMailer
        $this->phpMailerAvailable = $this->loadPHPMailer();
        
        // Initialize PHPMailer if available
        if ($this->phpMailerAvailable) {
            try {
                $PHPMailerClass = 'PHPMailer\PHPMailer\PHPMailer';
                $this->mailer = new $PHPMailerClass(true);
                $this->configureSMTP();
            } catch (Exception $e) {
                $this->lastError = "Failed to initialize PHPMailer: " . $e->getMessage();
                $this->phpMailerAvailable = false;
            }
        } else {
            $this->lastError = "PHPMailer not available - will use file fallback only";
        }
    }
    
    /**
     * Try to load PHPMailer classes
     */
    private function loadPHPMailer() {
        // Check if PHPMailer is already loaded
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return true;
        }
        
        // Try to autoload via Composer
        $autoloadPath = __DIR__ . '/vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
            return class_exists('PHPMailer\PHPMailer\PHPMailer');
        }
        
        return false;
    }
    
    /**
     * Get default configuration if config file is missing
     */
    private function getDefaultConfig() {
        return [
            'from_email' => 'noreply@example.com',
            'from_name' => 'Website Contact Form',
            'admin_email' => 'admin@example.com',
            'smtp_host' => 'localhost',
            'smtp_port' => 25,
            'smtp_secure' => '',
            'smtp_auth' => false,
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_debug' => 0
        ];
    }
    
    /**
     * Configure SMTP settings from config
     */
    private function configureSMTP() {
        if (!$this->phpMailerAvailable || !$this->mailer) {
            return;
        }
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp_host'];
            $this->mailer->SMTPAuth = $this->config['smtp_auth'];
            $this->mailer->Username = $this->config['smtp_username'];
            $this->mailer->Password = $this->config['smtp_password'];
            $this->mailer->SMTPSecure = $this->config['smtp_secure'];
            $this->mailer->Port = $this->config['smtp_port'];
            $this->mailer->CharSet = $this->config['charset'];
            
            // Debug mode (0 = off, 1 = client, 2 = server, 3 = connection, 4 = lowlevel)
            if (isset($this->config['debug_mode'])) {
                $this->mailer->SMTPDebug = $this->config['debug_mode'];
            }
            
            // Set default From address
            $this->mailer->setFrom(
                $this->config['from_email'],
                $this->config['from_name']
            );
        } catch (Exception $e) {
            $this->lastError = "SMTP configuration error: " . $e->getMessage();
            error_log($this->lastError);
        }
    }
    
    /**
     * Send a simple email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param bool $isHTML Whether body is HTML (default: false)
     * @param string|null $replyTo Reply-to email address
     * @param string|null $replyToName Reply-to name
     * @return bool Success status
     */
    public function sendEmail($to, $subject, $body, $isHTML = false, $replyTo = null, $replyToName = '') {
        // If PHPMailer is not available, save to file immediately
        if (!$this->phpMailerAvailable) {
            error_log("PHPMailer not available - saving email to file");
            return $this->saveToFile($to, $subject, $body);
        }
        
        try {
            // Clear any previous recipients
            $this->mailer->clearAddresses();
            $this->mailer->clearReplyTos();
            
            // Set recipient
            $this->mailer->addAddress($to);
            
            // Set reply-to if provided
            if ($replyTo) {
                $this->mailer->addReplyTo($replyTo, $replyToName);
            }
            
            // Content
            $this->mailer->isHTML($isHTML);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            // If HTML, set alternate plain text body
            if ($isHTML) {
                $this->mailer->AltBody = strip_tags($body);
            }
            
            // Send email
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Email sent successfully to: $to");
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            $this->lastError = "Email send error: " . $this->mailer->ErrorInfo;
            error_log($this->lastError);
            
            // Fallback: Save to file
            return $this->saveToFile($to, $subject, $body);
        }
    }
    
    /**
     * Send contact form email
     * 
     * @param array $formData Form data array
     * @return bool Success status
     */
    public function sendContactForm($formData) {
        // If PHPMailer is not available, save to file immediately
        if (!$this->phpMailerAvailable) {
            error_log("PHPMailer not available - saving contact form to file");
            $body = $this->buildContactFormHTML($formData);
            return $this->saveToFile(
                $this->config['admin_email'],
                'New Contact Form Submission: ' . $formData['subject'],
                $body
            );
        }
        
        $adminEmail = $this->config['admin_email'];
        $subject = "Contact Form Submission: " . $formData['subject'];
        
        // Create HTML body
        $body = $this->buildContactFormHTML($formData);
        
        // Send email to admin with reply-to set to the form submitter
        $adminEmailSent = $this->sendEmail(
            $adminEmail,
            $subject,
            $body,
            true,  // HTML email
            $formData['email'],
            $formData['fullName']
        );
        
        // Also send a copy to the submitter
        $submitterSubject = "Thank you for contacting us - " . $formData['subject'];
        $submitterBody = $this->buildSubmitterCopyHTML($formData);
        
        $this->sendEmail(
            $formData['email'],
            $submitterSubject,
            $submitterBody,
            true,
            $adminEmail,
            $this->config['from_name']
        );
        
        return $adminEmailSent;
    }
    
    /**
     * Send security alert email
     * 
     * @param string $alertType Type of security alert
     * @param array $details Alert details
     * @return bool Success status
     */
    public function sendSecurityAlert($alertType, $details) {
        // If PHPMailer is not available, just log it
        if (!$this->phpMailerAvailable) {
            error_log("Security Alert ($alertType): " . print_r($details, true));
            return true; // Don't fail on security alerts
        }
        
        $supportEmail = $this->config['support_email'];
        $subject = "🚨 Form Security Alert - $alertType";
        
        $body = "Security Alert: $alertType\n\n";
        $body .= "Form: Contact Form\n";
        $body .= "Date/Time: " . date('Y-m-d H:i:s') . "\n";
        $body .= "IP Address: " . ($details['ip'] ?? 'Unknown') . "\n";
        $body .= "User Agent: " . ($details['user_agent'] ?? 'Unknown') . "\n\n";
        
        if (isset($details['error_codes'])) {
            $body .= "Error Codes: " . implode(', ', $details['error_codes']) . "\n";
        }
        
        if (isset($details['honeypot_value'])) {
            $body .= "Honeypot Value: " . $details['honeypot_value'] . "\n";
        }
        
        $body .= "\nThis was likely a bot or malicious attempt.";
        
        return $this->sendEmail($supportEmail, $subject, $body, false);
    }
    
    /**
     * Build HTML email body for contact form
     */
    private function buildContactFormHTML($formData) {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #4b5563; margin-bottom: 5px; }
        .value { color: #1f2937; padding: 10px; background: white; border-radius: 4px; }
        .footer { background: #f3f4f6; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 New Contact Form Submission</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">' . htmlspecialchars($formData['fullName']) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">' . htmlspecialchars($formData['email']) . '</div>
            </div>
            
            ' . (!empty($formData['phone']) ? '
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">' . htmlspecialchars($formData['phone']) . '</div>
            </div>
            ' : '') . '
            
            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">' . htmlspecialchars($formData['subject']) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">' . nl2br(htmlspecialchars($formData['message'])) . '</div>
            </div>
        </div>
        <div class="footer">
            Submitted on ' . date('F j, Y \a\t g:i A') . ' from IP ' . $_SERVER['REMOTE_ADDR'] . '
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Build HTML email body for submitter's copy
     */
    private function buildSubmitterCopyHTML($formData) {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #484c9b 0%, #5965af 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #4b5563; margin-bottom: 5px; }
        .value { color: #1f2937; padding: 10px; background: white; border-radius: 4px; }
        .footer { background: #f3f4f6; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 8px 8px; }
        .thank-you { background: #ecfdf5; border: 2px solid #10b981; color: #065f46; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Thank You for Contacting Us!</h1>
        </div>
        <div class="content">
            <div class="thank-you">
                <h2 style="margin: 0 0 10px;">Message Received</h2>
                <p style="margin: 0;">Hi ' . htmlspecialchars($formData['fullName']) . ', thank you for reaching out! We\'ve received your message and will get back to you as soon as possible.</p>
            </div>
            
            <h3 style="color: #484c9b;">Your Submission Details:</h3>
            
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">' . htmlspecialchars($formData['fullName']) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">' . htmlspecialchars($formData['email']) . '</div>
            </div>
            
            ' . (!empty($formData['phone']) ? '
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">' . htmlspecialchars($formData['phone']) . '</div>
            </div>
            ' : '') . '
            
            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">' . htmlspecialchars($formData['subject']) . '</div>
            </div>
            
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">' . nl2br(htmlspecialchars($formData['message'])) . '</div>
            </div>
        </div>
        <div class="footer">
            This is a copy of your submission sent on ' . date('F j, Y \a\t g:i A') . '<br>
            Please do not reply to this email. We will contact you directly.
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Fallback: Save email to file if sending fails
     */
    private function saveToFile($to, $subject, $body) {
        try {
            $submissionsDir = __DIR__ . '/email_submissions';
            if (!is_dir($submissionsDir)) {
                mkdir($submissionsDir, 0755, true);
            }
            
            $filename = $submissionsDir . '/email_' . date('Y-m-d_His') . '.txt';
            $fileContent = "=== EMAIL MESSAGE (Failed to send) ===\n\n";
            $fileContent .= "To: $to\n";
            $fileContent .= "Subject: $subject\n";
            $fileContent .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
            $fileContent .= "Body:\n";
            $fileContent .= strip_tags($body) . "\n\n";
            $fileContent .= "Error: " . $this->lastError . "\n";
            
            if (file_put_contents($filename, $fileContent)) {
                error_log("Email saved to file: $filename");
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Failed to save email to file: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get last error message
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Test email configuration
     */
    public function testConnection() {
        try {
            return $this->mailer->smtpConnect();
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
