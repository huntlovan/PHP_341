<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * ContactController.php - Controller for contact form processing
 * Handles form validation and email sending
 * 
 * Dependencies: called from contactForm.php (work in progress) and depends on emailHelper.php
 *
 */

class ContactController {
    private $emailHelper;
    
    public function __construct($emailHelper) {
        $this->emailHelper = $emailHelper;
    }
    
    /**
     * Process contact form submission
     * @return array Result with status, message, and data
     */
    public function processContactForm($postData) {
        $result = [
            'success' => false,
            'message' => '',
            'data' => [],
            'redirect' => null
        ];
        
        // Honeypot validation
        $honeypot = trim($postData['website_url'] ?? '');
        
        if ($honeypot !== '') {
            // Bot detected - honeypot was filled
            error_log("Honeypot triggered - Bot detected from IP: " . $_SERVER['REMOTE_ADDR']);
            
            // Send security alert
            try {
                $this->emailHelper->sendSecurityAlert('Honeypot Protection Triggered', [
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                    'honeypot_value' => $honeypot
                ]);
            } catch (Exception $e) {
                error_log("Failed to send security alert: " . $e->getMessage());
            }
            
            $result['redirect'] = 'processEmailForm.php?denied=1';
            return $result;
        }
        
        // Collect and sanitize form data
        $formData = [
            'fullName' => trim($postData['fullName'] ?? ''),
            'email' => trim($postData['email'] ?? ''),
            'phone' => trim($postData['phone'] ?? ''),
            'subject' => trim($postData['subject'] ?? ''),
            'message' => trim($postData['message'] ?? '')
        ];
        
        // Validate required fields
        if (empty($formData['fullName']) || empty($formData['email']) || 
            empty($formData['subject']) || empty($formData['message'])) {
            $result['message'] = 'Please fill in all required fields.';
            return $result;
        }
        
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $result['message'] = 'Please enter a valid email address.';
            return $result;
        }
        
        // Send email
        try {
            $emailSent = $this->emailHelper->sendContactForm($formData);
            
            if ($emailSent) {
                $result['success'] = true;
                $result['message'] = 'Your message has been sent successfully! We\'ll get back to you soon.';
                $result['data'] = $formData;
                $result['redirect'] = 'index_v1.php?contact_success=1';
            } else {
                $result['success'] = true;
                $result['message'] = 'Your message has been received and saved. We\'ll respond as soon as possible.';
                $result['data'] = $formData;
                $result['redirect'] = 'index_v1.php?contact_success=1';
            }
        } catch (Exception $e) {
            error_log("EmailHelper exception: " . $e->getMessage());
            
            // Emergency fallback - save to file
            if ($this->saveToFile($formData, $e->getMessage())) {
                $result['success'] = true;
                $result['message'] = 'Your message has been received. We\'ll get back to you soon.';
                $result['data'] = $formData;
                $result['redirect'] = 'index_v1.php?contact_success=1';
            } else {
                $result['message'] = 'Sorry, there was an error processing your message. Please try again later.';
            }
        }
        
        return $result;
    }
    
    /**
     * Emergency fallback to save submission to file
     */
    private function saveToFile($formData, $error) {
        $submissionsDir = __DIR__ . '/../email_submissions';
        if (!is_dir($submissionsDir)) {
            @mkdir($submissionsDir, 0755, true);
        }
        
        $filename = $submissionsDir . '/submission_' . date('Y-m-d_His') . '.txt';
        $fileContent = "=== CONTACT FORM SUBMISSION (EMERGENCY FALLBACK) ===\n\n";
        $fileContent .= "Name: " . $formData['fullName'] . "\n";
        $fileContent .= "Email: " . $formData['email'] . "\n";
        $fileContent .= "Phone: " . ($formData['phone'] ?: 'Not provided') . "\n";
        $fileContent .= "Subject: " . $formData['subject'] . "\n\n";
        $fileContent .= "Message:\n" . $formData['message'] . "\n\n";
        $fileContent .= "---\n";
        $fileContent .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $fileContent .= "Error: " . $error . "\n";
        
        return @file_put_contents($filename, $fileContent) !== false;
    }
}
