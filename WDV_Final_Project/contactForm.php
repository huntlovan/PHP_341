<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * contactForm.php - input form for Contact form with honeypot protection
 * 
 * Dependencies: called from index_v1.php (work in progress)
 * ToDo: load Mimi's Bakery phone and web site from a configuration data storage or file.
 */
session_start();

// Check for error message from processEmailForm
$errorMessage = '';
if (isset($_SESSION['contact_error'])) {
    $errorMessage = $_SESSION['contact_error'];
    unset($_SESSION['contact_error']);
}

// Set page-specific variables
$pageTitle = 'Contact Us - Mimi\'s Bakery';
$headerTitle = 'Contact Us';
$headerSubtitle = 'We\'d love to hear from you!';

// Include header
include __DIR__ . '/includes/header.php';
?>
    <style>
        /* Page-specific styles for contactForm.php */
        
        .form-container {
            max-width: 650px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            margin: 0 0 12px;
            font-size: 32px;
            font-weight: 700;
            color: #484c9b;
            text-align: center;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin: 0 0 32px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 15px;
        }
        
        input[type="text"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            box-sizing: border-box;
            font-family: 'Open Sans', Arial, sans-serif;
            transition: border-color 0.3s;
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #5965af;
        }
        
        textarea {
            min-height: 140px;
            resize: vertical;
        }
        
        .required {
            color: #dc2626;
            font-weight: bold;
        }
        
        .note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
            line-height: 1.5;
        }
        
        .security-note {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        button,
        .btn {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        
        button[type="submit"] {
            background: linear-gradient(135deg, #484c9b 0%, #5965af 100%);
            color: white;
        }
        
        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(72, 76, 155, 0.4);
        }
        
        .btn-cancel {
            background: #e5e7eb;
            color: #333;
        }
        
        .btn-cancel:hover {
            background: #d1d5db;
        }
        
        .note {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
            line-height: 1.5;
        }
        
        .security-note {
            background: #f0fdf4;
            border: 1px solid #86efac;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 24px;
            font-size: 14px;
            color: #166534;
            text-align: center;
        }
        
        .error-message {
            background: #fef2f2;
            border: 2px solid #dc2626;
            color: #991b1b;
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 600;
            text-align: center;
        }
        
        /* Honeypot field - hidden from users */
        .honeypot {
            position: absolute;
            left: -10000px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
    </style>
</head>
<body>
<?php
// Include navigation and banner
include __DIR__ . '/includes/nav-header.php';
?>

    <div class="container">
        <div class="form-container">
        <h1>📬 Contact Us</h1>
        <p class="subtitle">We'd love to hear from you. Send us a message!</p>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message">
                ❌ <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="processEmailForm.php">
            <!-- Honeypot fallback -->
            <div class="honeypot" aria-hidden="true">
                <label for="website_url">Leave this field empty</label>
                <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="fullName">Full Name <span class="required">*</span></label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <div class="note">We'll never share your email with anyone else.</div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Optional">
            </div>
            
            <div class="form-group">
                <label for="subject">Subject <span class="required">*</span></label>
                <select id="subject" name="subject" required>
                    <option value="">-- Select a subject --</option>
                    <option value="General Inquiry">General Inquiry</option>
                    <option value="Support Request">Support Request</option>
                    <option value="Event Information">Event Information</option>
                    <option value="Partnership">Partnership Opportunity</option>
                    <option value="Feedback">Feedback</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" required placeholder="Please enter your message here..."></textarea>
            </div>
            
            <div class="button-group">
                <button type="submit" name="submit">📤 Send Message</button>
                <a href="index_v1.php" class="btn btn-cancel">Cancel</a>
            </div>
            
            <div class="security-note">
                🔒 This form is protected by honeypot security measures.
            </div>
        </form>
        </div>
    </div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
