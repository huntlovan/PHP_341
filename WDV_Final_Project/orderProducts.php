<?php
session_start();

// Set page-specific variables
$pageTitle = 'Order Products - Mimi\'s Bakery';
$headerTitle = '🛒 Order Products';
$headerSubtitle = 'Place your order for Mimi\'s delicious baked goods';

// Include header
include __DIR__ . '/includes/header.php';
?>
    <style>
        /* Page-specific styles for orderProducts.php */
        .order-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-container h2 {
            color: #484c9b;
            margin-bottom: 20px;
            font-size: 28px;
        }
        
        .order-container p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
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
        
        .info-message {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php
// Include navigation and banner
include __DIR__ . '/includes/nav-header.php';
?>

    <!-- Main Content -->
    <div class="container">
        <div class="info-message">
            ℹ️ Browse our products and add items to your cart to place an order
        </div>

        <div class="order-container">
            <h2>How to Order</h2>
            <p>
                <strong>Step 1:</strong> Browse our bakery items to see all available products.<br>
                <strong>Step 2:</strong> Select quantities for items you'd like to order.<br>
                <strong>Step 3:</strong> Add items to your cart.<br>
                <strong>Step 4:</strong> Click "Place Order" and provide your contact information.<br>
                <strong>Step 5:</strong> Review your order and confirm!
            </p>
            
            <p style="margin-top: 30px; text-align: center;">
                <a href="baked-products.php" class="cta-button">Start Shopping →</a>
            </p>
        </div>
    </div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
