<?php
/*******************************************************
 * @package  wdv341_final_project
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * order-confirmation.php - VIEW for order confirmation display
 * 
 * Dependencies: called from baked-products.php
 * ToDo: load Mimi's Bakery phone and web site from a configuration data storage or file.
 */

session_start();

// Check if order success data exists
if (!isset($_SESSION['order_success'])) {
    header('Location: baked-products.php');
    exit;
}

$orderInfo = $_SESSION['order_success'];

// Clear the session data after retrieving it
unset($_SESSION['order_success']);
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Confirmation - Mimi Bakery Store (www.mimisBakery.com)</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,:after,:before{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}
        body{font:normal 15px/25px 'Open Sans',Arial,Helvetica,sans-serif;color:#444;text-align:left;background:#f5f5f5;margin:0;padding:20px}
        h1,h2,h3{font-weight:400}
        h1{font:normal 40px/50px 'Open Sans',Arial,Helvetica,sans-serif;text-align:center;color:#444;margin:0 0 20px}
        h1 span{color:#484c9b}
        h2{font-size:25px;line-height:30px;color:#484c9b;margin:30px 0 15px}
        h3{font-size:18px;line-height:25px;margin:20px 0 10px}
        a{color:#484c9b;text-decoration:none}
        a:hover{text-decoration:underline}
        
        .container{max-width:800px;margin:0 auto;background:#fff;padding:40px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .success-header{text-align:center;padding:30px 0;border-bottom:2px solid #5965af}
        .success-icon{font-size:60px;color:#4CAF50;margin-bottom:20px}
        .confirmation-number{font-size:32px;font-weight:700;color:#5965af;margin:20px 0}
        .order-details{margin:30px 0}
        .detail-row{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid #eee}
        .detail-label{font-weight:600;color:#666}
        .detail-value{color:#333}
        
        .order-items{margin:30px 0}
        .item-table{width:100%;border-collapse:collapse;margin-top:15px}
        .item-table thead{background:#5965af;color:#fff}
        .item-table th,.item-table td{padding:12px;text-align:left;border-bottom:1px solid #eee}
        .item-table th{font-weight:600}
        .item-table tbody tr:hover{background:#f9f9f9}
        .item-table .text-right{text-align:right}
        .item-table .text-center{text-align:center}
        
        .order-total{margin-top:20px;padding-top:20px;border-top:3px solid #5965af}
        .total-row{display:flex;justify-content:space-between;align-items:center;padding:15px 0}
        .total-label{font-size:24px;font-weight:700;color:#333}
        .total-value{font-size:28px;font-weight:700;color:#5965af}
        
        .action-buttons{margin-top:40px;text-align:center;display:flex;gap:15px;justify-content:center}
        .btn{display:inline-block;padding:15px 30px;border-radius:6px;font-size:16px;font-weight:600;text-decoration:none;transition:transform .2s,background .2s}
        .btn-primary{background:#5965af;color:#fff}
        .btn-primary:hover{background:#484c9b;transform:translateY(-2px)}
        .btn-secondary{background:#fff;color:#5965af;border:2px solid #5965af}
        .btn-secondary:hover{background:#f5f5f5;transform:translateY(-2px)}
        
        .print-btn{margin-top:20px}
        @media print{
            .action-buttons,.print-btn{display:none}
            body{background:#fff}
            .container{box-shadow:none;padding:20px}
        }
        
        @media only screen and (max-width:768px){
            .container{padding:20px}
            .detail-row{flex-direction:column;gap:5px}
            .action-buttons{flex-direction:column}
            .btn{width:100%}
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your order. Your order has been successfully placed.</p>
            <div class="confirmation-number">
                <?= htmlspecialchars($orderInfo['confirmation_number']) ?>
            </div>
            <p style="color:#666;font-size:14px;margin-top:10px">
                Please save this confirmation number for your records
            </p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">#<?= $orderInfo['order_id'] ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value"><?= date('F j, Y g:i A', strtotime($orderInfo['order_date'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer Name:</span>
                <span class="detail-value"><?= htmlspecialchars($orderInfo['customer_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone Number:</span>
                <span class="detail-value"><?= htmlspecialchars($orderInfo['customer_phone']) ?></span>
            </div>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            <table class="item-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderInfo['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-right">$<?= number_format($item['price'], 2) ?></td>
                            <td class="text-right">$<?= number_format($item['item_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="order-total">
            <div class="total-row">
                <span class="total-label">Order Total:</span>
                <span class="total-value">$<?= number_format($orderInfo['order_total'], 2) ?></span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="baked-products.php" class="btn btn-primary">Continue Shopping</a>
            <a href="javascript:window.print()" class="btn btn-secondary">Print Confirmation</a>
        </div>

        <div class="print-btn" style="text-align:center;margin-top:30px;padding-top:30px;border-top:1px solid #eee">
            <p style="color:#666;font-size:14px">
                Questions about your order? Contact us at mimibakery@gmail.com or call (515) 453-4040 or use our web site at www.mimisBakery.com
            </p>
        </div>
    </div>
</body>

</html>
