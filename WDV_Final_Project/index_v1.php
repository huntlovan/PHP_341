<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * index_v1.php - Landing page with main navigation
 * 
 * Dependencies: called from index.html (work in progress)
 * ToDo: load Mimi's Bakery phone and web site from a configuration data storage or file.
 */
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['validUser']) && $_SESSION['validUser'] === true;
$username = $_SESSION['username'] ?? 'Guest';

// Check for contact form success message
$contactSuccess = false;
$contactMessage = '';
$contactData = [];

if (isset($_GET['contact_success']) && $_GET['contact_success'] == 1) {
    $contactSuccess = true;
    $contactMessage = $_SESSION['contact_message'] ?? 'Your message has been sent successfully!';
    $contactData = $_SESSION['contact_data'] ?? [];
    
    // Clear session data
    unset($_SESSION['contact_message']);
    unset($_SESSION['contact_data']);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mimi famous pineaple cake and more - Home</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="shared-styles.css">
    <style>
        /* Page-specific styles for index_v1.php */
        
        .hero {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .hero h2 {
            font-size: 32px;
            color: #484c9b;
            margin-bottom: 15px;
        }
        
        .hero p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .cta-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #484c9b 0%, #5965af 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(72, 76, 155, 0.3);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(72, 76, 155, 0.4);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }
        
        .feature-card h3 {
            color: #484c9b;
            font-size: 22px;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .feature-card a {
            display: inline-block;
            padding: 10px 25px;
            background-color: #484c9b;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        
        .feature-card a:hover {
            background-color: #5965af;
        }
        
        .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .login-message {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #059669;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
        
        .contact-success {
            background: #ecfdf5;
            border: 2px solid #10b981;
            color: #065f46;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 40px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        
        .contact-success h3 {
            margin: 0 0 15px;
            font-size: 24px;
            color: #065f46;
        }
        
        .contact-success p {
            margin: 10px 0;
            font-size: 16px;
        }
        
        .contact-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: left;
        }
        
        .contact-detail-row {
            padding: 8px 0;
            border-bottom: 1px solid #d1fae5;
        }
        
        .contact-detail-row:last-child {
            border-bottom: none;
        }
        
        .contact-detail-label {
            font-weight: 600;
            color: #047857;
            margin-right: 8px;
        }
        
        .action-section {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .browse,
        .order {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-align: center;
            flex: 0 1 400px;
            transition: all 0.3s ease;
        }
        
        .browse:hover,
        .order:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }
        
        .browse h2,
        .order h2 {
            color: #484c9b;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .browse p,
        .order p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .action-section {
                flex-direction: column;
                gap: 20px;
            }
            
            .browse,
            .order {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark bg-primary">
        <ul>
            <li><a href="index_v1.php">🏠 Home</a></li>
            <li><a href="baked-products.php">🛍️ View Bakery</a></li>
            <li><a href="contactForm.php">📬 Contact Us</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="login_v1.php">⚙️ Admin Panel</a></li>
                <li><a href="logout_v1.php" class="logout">🔓 Logout</a></li>
            <?php else: ?>
                <li><a href="login_v1.php" class="login">🔐 Admin Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Header -->
    <header>
        <h2 class="pb-4 mb-4 fst-italic">
            Mimi's love
        </h2>
        <p>
            For many years, <b>my mom, mimi</b> has been baking the cookies and cakes at home and sharing her love with family. In 2019, she
            started to take orders from co-workers, and 
            everything she baked turns out to be everyone's new favorites. Everyone at her company
            loves the soft cakes, not too sweet frosting, and chocolate combination cookies... 
            
            After receiving many orders, it is clear that mimi has built up a large list of clients!
        </p>
        <p>
            Mimi is famous for using only the best ingredients, and evrything is make from scratch, no short cut, and the result
            are absolutely soft and delicious dessert that you'll ever have.
            
        </p>
        <?php if ($isLoggedIn): ?>
            <div class="user-info">👤 Logged in as: <strong><?php echo htmlspecialchars($username); ?></strong></div>
        <?php endif; ?>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if ($contactSuccess): ?>
            <div class="contact-success">
                <h3>✅ Message Sent Successfully!</h3>
                <p><?php echo htmlspecialchars($contactMessage); ?></p>
                
                <?php if (!empty($contactData)): ?>
                <div class="contact-details">
                    <strong style="color: #065f46;">Your Submission:</strong>
                    
                    <div class="contact-detail-row">
                        <span class="contact-detail-label">Name:</span>
                        <span><?php echo htmlspecialchars($contactData['fullName'] ?? 'N/A'); ?></span>
                    </div>
                    
                    <div class="contact-detail-row">
                        <span class="contact-detail-label">Email:</span>
                        <span><?php echo htmlspecialchars($contactData['email'] ?? 'N/A'); ?></span>
                    </div>
                    
                    <?php if (!empty($contactData['phone'])): ?>
                    <div class="contact-detail-row">
                        <span class="contact-detail-label">Phone:</span>
                        <span><?php echo htmlspecialchars($contactData['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-detail-row">
                        <span class="contact-detail-label">Subject:</span>
                        <span><?php echo htmlspecialchars($contactData['subject'] ?? 'N/A'); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <p style="margin-top: 20px; font-size: 14px; color: #047857;">
                    We'll get back to you as soon as possible!
                </p>
            </div>
        <?php endif; ?>
        
        <?php if ($isLoggedIn): ?>
            <div class="login-message">
                ✅ Welcome back, <?php echo htmlspecialchars($username); ?>! You have access to all features.
            </div>
        <?php endif; ?>

        <div class="action-section">
            <div class="browse">
                <h2>Ready to Order</h2>
                <p>Explore our delicious selection of freshly baked cakes, cookies, and treats made with love by Mimi.</p>
                <a href="baked-products.php" class="cta-button">Browse Our Bakery Items →</a>
            </div>

            <div class="order">
                <h2>How to Order</h2>
                <p>Place your order for Mimi's famous baked goods and experience homemade quality delivered to you.</p>
                <a href="orderProducts.php" class="cta-button">Step by Step →</a>
            </div>
        </div>
    </div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
