<?php
/**
 * baked-products.php - VIEW for product catalog display
 * Implements MVC: Uses ProductModel (data) and ProductController (logic)
 * ToDo: see if any of the HTML can be moved to a separate file for easier maintenance.
 */
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['validUser']) && $_SESSION['validUser'] === true;
$username = $_SESSION['username'] ?? 'Guest';

// Set page-specific variables for header
$pageTitle = 'Bakery Products - Mimi\'s Bakery';
$headerTitle = 'DMACC Electronics Store!';
$headerSubtitle = 'Products for your Home and School Office';

// Check for order errors from session
$orderError = null;
if (isset($_SESSION['order_error'])) {
    $orderError = $_SESSION['order_error'];
    unset($_SESSION['order_error']);
}

// MVC: Load Model and Controller
require_once __DIR__ . '/models/ProductModel.php';
require_once __DIR__ . '/controllers/ProductController.php';

// Get database connection
ob_start();
require_once __DIR__ . '/dbConnect1.php';
ob_end_clean();

// Initialize Model and Controller
$products = [];
try {
    if (isset($pdo) && $pdo instanceof PDO) {
        $productModel = new ProductModel($pdo);
        $productController = new ProductController($productModel);
        
        // Get products formatted for display (Controller handles formatting)
        $products = $productController->getProductsForDisplay();
    }
} catch (Throwable $e) {
    error_log("Error loading products: " . $e->getMessage());
    $products = [];
}

// Include header
include __DIR__ . '/includes/header.php';
?>
    <style>
        /* Page-specific styles for baked-products.php */
        section {display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; max-width: 1200px;}
        .productBlock{width:calc(100% / 6 - 1rem);display:inline-block;border:none;padding:1rem;background:#efefef;border-radius:10px;font-size:.875rem;line-height:1.5}
        .productImage img{display:block;margin-left:auto;margin-right:auto;width:100%;height:auto}
        .productName{font-size:large;margin:1rem 0 .5rem;text-align:left}
        .productDesc{margin-left:10px;margin-right:10px;margin:0}
        .productPrice{font-size:larger;color:#00f;margin:.5rem 0;text-align:left}
        .productStatus{font-weight:bolder;color:#2f4f4f;margin:.5rem 0;text-align:left}
        .productInventory{margin:.5rem 0;text-align:left}
        .productLowInventory{color:red}
        /* Order controls */
        .qty-controls{display:flex;align-items:center;gap:8px;margin:12px 0}
        .qty-btn{width:32px;height:32px;border:1px solid #484c9b;background:#fff;color:#484c9b;border-radius:4px;font-size:18px;font-weight:bold;cursor:pointer;display:flex;align-items:center;justify-content:center}
        .qty-btn:hover{background:#484c9b;color:#fff}
        .qty-input{width:50px;text-align:center;border:1px solid #ccc;border-radius:4px;padding:6px;font-size:14px}
        .add-to-order{width:100%;padding:10px;background:#5965af;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:600;cursor:pointer;margin-top:8px}
        .add-to-order:hover{background:#484c9b}
        .add-to-order:disabled{background:#ccc;cursor:not-allowed}
        .order-bar{position:sticky;top:0;background:#5965af;color:#fff;padding:16px;text-align:center;z-index:1000;box-shadow:0 2px 8px rgba(0,0,0,0.2)}
        .order-bar-content{max-width:1200px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
        .order-summary{font-size:18px;font-weight:600}
        .place-order-btn{padding:12px 24px;background:#fff;color:#5965af;border:none;border-radius:6px;font-size:16px;font-weight:700;cursor:pointer;transition:transform .2s}
        .place-order-btn:hover{transform:scale(1.05)}
        .place-order-btn:disabled{background:#ccc;color:#666;cursor:not-allowed;transform:none}
        .bottom-order-bar{background:#5965af;color:#fff;padding:20px;text-align:center;margin-top:40px}
        /* Error message styling */
        .error-message{background:#f44336;color:#fff;padding:15px 20px;margin:20px auto;max-width:800px;border-radius:6px;text-align:center;font-weight:600;box-shadow:0 2px 8px rgba(244,67,54,0.3)}
        .error-message button{background:#fff;color:#f44336;border:none;padding:8px 15px;margin-left:15px;border-radius:4px;cursor:pointer;font-weight:600}
        .error-message button:hover{background:#ffebee}
        /* Customer form modal */
        .modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);z-index:9999;justify-content:center;align-items:center}
        .modal-overlay.active{display:flex}
        .modal-content{background:#fff;padding:40px;border-radius:10px;max-width:500px;width:90%;box-shadow:0 4px 20px rgba(0,0,0,0.3);animation:slideIn 0.3s ease}
        @keyframes slideIn{from{transform:translateY(-50px);opacity:0}to{transform:translateY(0);opacity:1}}
        .modal-header{text-align:center;margin-bottom:30px}
        .modal-header h2{color:#5965af;margin:0 0 10px;font-size:28px}
        .modal-header p{color:#666;margin:0;font-size:14px}
        .form-group{margin-bottom:20px}
        .form-group label{display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:14px}
        .form-group input{width:100%;padding:12px;border:2px solid #ddd;border-radius:6px;font-size:16px;transition:border-color .3s}
        .form-group input:focus{outline:none;border-color:#5965af}
        .form-group input.error{border-color:#f44336}
        .form-error{color:#f44336;font-size:12px;margin-top:5px;display:none}
        .form-error.active{display:block}
        .modal-buttons{display:flex;gap:15px;margin-top:30px}
        .modal-btn{flex:1;padding:14px;border:none;border-radius:6px;font-size:16px;font-weight:600;cursor:pointer;transition:transform .2s,background .3s}
        .modal-btn-primary{background:#5965af;color:#fff}
        .modal-btn-primary:hover{background:#484c9b;transform:translateY(-2px)}
        .modal-btn-secondary{background:#eee;color:#333}
        .modal-btn-secondary:hover{background:#ddd;transform:translateY(-2px)}
    </style>
</head>
<body>
<?php
// Include navigation and banner
include __DIR__ . '/includes/nav-header.php';
?>

    <!-- Top Order Bar -->
    <div class="order-bar">
        <div class="order-bar-content">
            <div class="order-summary">
                <span id="orderCount">0</span> items in order
            </div>
            <button class="place-order-btn" id="topPlaceOrderBtn" onclick="placeOrder()" disabled>
                🛒 Place Order
            </button>
        </div>
    </div>

    <?php if ($orderError): ?>
        <!-- Error Message -->
        <div class="error-message" id="errorMessage">
            ⚠️ <?= htmlspecialchars($orderError) ?>
            <button onclick="document.getElementById('errorMessage').style.display='none'">Dismiss</button>
        </div>
    <?php endif; ?>

    <header>
        <h1>Mimi\'s way!</h1>
        <p> refuse to serve anything which is less than perfect! - Mimi</p>
    </header>
    <section>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="productBlock" data-product-id="<?=$product['id']?>">
                    <div class="productImage">
                        <img src="<?='productImages/' . rawurlencode($product['image'])?>" alt="<?=$product['name']?>">
                    </div>
                    <p class="productName"><?=$product['name']?></p>
                    <p class="productDesc"><?=$product['description']?></p>
                    <p class="productPrice">$<?=$product['price']?></p>
                    <?php if ($product['status'] !== ''): ?>
                        <p class="productStatus"><?=$product['status']?></p>
                    <?php endif; ?>
                    <p class="<?=$product['inventoryClass']?>"><?="{$product['inStock']} In Stock!"?></p>
                    
                    <!-- Quantity Controls -->
                    <div class="qty-controls">
                        <button class="qty-btn" onclick="decreaseQty(<?=$product['id']?>)" aria-label="Decrease quantity">−</button>
                        <input type="number" 
                               id="qty-<?=$product['id']?>" 
                               class="qty-input" 
                               value="0" 
                               min="0" 
                               max="<?=$product['inStock']?>"
                               onchange="updateQty(<?=$product['id']?>, this.value)"
                               aria-label="Quantity">
                        <button class="qty-btn" onclick="increaseQty(<?=$product['id']?>, <?=$product['inStock']?>)" aria-label="Increase quantity">+</button>
                    </div>
                    
                    <button class="add-to-order" onclick="addToOrder(<?=$product['id']?>, '<?=addslashes($product['name'])?>', <?=$product['priceRaw']?>)">
                        Add to Order
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available.</p>
        <?php endif; ?>
    </section>

    <!-- Bottom Order Bar -->
    <div class="bottom-order-bar">
        <div class="order-bar-content">
            <div class="order-summary">
                Total Items: <span id="bottomOrderCount">0</span>
            </div>
            <button class="place-order-btn" id="bottomPlaceOrderBtn" onclick="placeOrder()" disabled>
                🛒 Place Order
            </button>
        </div>
    </div>

    <!-- Customer Information Modal -->
    <div class="modal-overlay" id="customerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Customer Information</h2>
                <p>Please provide your contact details to complete your order</p>
            </div>
            <form id="customerForm" onsubmit="return false;">
                <div class="form-group">
                    <label for="customerName">Full Name *</label>
                    <input type="text" 
                           id="customerName" 
                           name="customerName" 
                           placeholder="Enter your full name"
                           required>
                    <div class="form-error" id="nameError">Please enter your full name</div>
                </div>
                <div class="form-group">
                    <label for="customerPhone">Phone Number *</label>
                    <input type="tel" 
                           id="customerPhone" 
                           name="customerPhone" 
                           placeholder="(555) 123-4567"
                           maxlength="14"
                           required>
                    <div class="form-error" id="phoneError">Please enter a valid phone number</div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="closeCustomerModal()">
                        Cancel
                    </button>
                    <button type="button" class="modal-btn modal-btn-primary" onclick="submitOrderWithCustomerInfo()">
                        Complete Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Shopping cart object to store product_id and quantity
        let cart = {};

        // Update order count display
        function updateOrderCount() {
            let totalItems = 0;
            for (let productId in cart) {
                totalItems += cart[productId].quantity;
            }
            
            document.getElementById('orderCount').textContent = totalItems;
            document.getElementById('bottomOrderCount').textContent = totalItems;
            
            // Enable/disable place order buttons
            const hasItems = totalItems > 0;
            document.getElementById('topPlaceOrderBtn').disabled = !hasItems;
            document.getElementById('bottomPlaceOrderBtn').disabled = !hasItems;
        }

        // Decrease quantity by 1
        function decreaseQty(productId) {
            const input = document.getElementById('qty-' + productId);
            let value = parseInt(input.value) || 0;
            if (value > 0) {
                value--;
                input.value = value;
                updateQty(productId, value);
            }
        }

        // Increase quantity by 1
        function increaseQty(productId, maxStock) {
            const input = document.getElementById('qty-' + productId);
            let value = parseInt(input.value) || 0;
            if (value < maxStock) {
                value++;
                input.value = value;
                updateQty(productId, value);
            } else {
                alert('Maximum stock reached for this product.');
            }
        }

        // Update quantity (called when user types in input)
        function updateQty(productId, value) {
            const qty = parseInt(value) || 0;
            const input = document.getElementById('qty-' + productId);
            const maxStock = parseInt(input.max);
            
            // Validate quantity
            if (qty < 0) {
                input.value = 0;
            } else if (qty > maxStock) {
                input.value = maxStock;
                alert('Quantity cannot exceed available stock (' + maxStock + ')');
            }
        }

        // Add product to order
        function addToOrder(productId, productName, price) {
            const qtyInput = document.getElementById('qty-' + productId);
            const quantity = parseInt(qtyInput.value) || 0;
            
            if (quantity === 0) {
                alert('Please select a quantity greater than 0');
                return;
            }
            
            // Add or update cart
            if (cart[productId]) {
                cart[productId].quantity += quantity;
            } else {
                cart[productId] = {
                    name: productName,
                    price: price,
                    quantity: quantity
                };
            }
            
            // Reset quantity input
            qtyInput.value = 0;
            
            // Update display
            updateOrderCount();
            
            // Show confirmation
            alert(quantity + ' x ' + productName + ' added to order!');
        }

        // Place order - shows customer information modal
        function placeOrder() {
            if (Object.keys(cart).length === 0) {
                alert('Your order is empty. Please add items first.');
                return;
            }
            
            // Show customer information modal
            document.getElementById('customerModal').classList.add('active');
            
            // Focus on name input
            setTimeout(() => {
                document.getElementById('customerName').focus();
            }, 300);
        }

        // Close customer modal
        function closeCustomerModal() {
            document.getElementById('customerModal').classList.remove('active');
            // Clear any error states
            document.getElementById('customerName').classList.remove('error');
            document.getElementById('customerPhone').classList.remove('error');
            document.getElementById('nameError').classList.remove('active');
            document.getElementById('phoneError').classList.remove('active');
        }

        // Format phone number as user types
        function formatPhoneNumber(input) {
            // Get only digits
            const digits = input.value.replace(/\D/g, '');
            
            // Format based on length
            let formatted = '';
            if (digits.length <= 3) {
                formatted = digits;
            } else if (digits.length <= 6) {
                formatted = '(' + digits.slice(0, 3) + ') ' + digits.slice(3);
            } else {
                formatted = '(' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6, 10);
            }
            
            input.value = formatted;
        }

        // Add phone formatting on input
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('customerPhone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    formatPhoneNumber(this);
                });
            }
        });

        // Validate customer information
        function validateCustomerInfo() {
            let isValid = true;
            const nameInput = document.getElementById('customerName');
            const phoneInput = document.getElementById('customerPhone');
            const nameError = document.getElementById('nameError');
            const phoneError = document.getElementById('phoneError');
            
            // Reset error states
            nameInput.classList.remove('error');
            phoneInput.classList.remove('error');
            nameError.classList.remove('active');
            phoneError.classList.remove('active');
            
            // Validate name (at least 2 characters)
            const name = nameInput.value.trim();
            if (name.length < 2) {
                nameInput.classList.add('error');
                nameError.classList.add('active');
                nameError.textContent = 'Please enter your full name (at least 2 characters)';
                isValid = false;
            }
            
            // Validate phone number
            const phone = phoneInput.value.trim();
            const phoneDigits = phone.replace(/\D/g, '');
            
            // Check if empty
            if (phone.length === 0) {
                phoneInput.classList.add('error');
                phoneError.classList.add('active');
                phoneError.textContent = 'Phone number is required';
                isValid = false;
            }
            // Check minimum length (10 digits for US numbers)
            else if (phoneDigits.length < 10) {
                phoneInput.classList.add('error');
                phoneError.classList.add('active');
                phoneError.textContent = 'Phone number must be at least 10 digits';
                isValid = false;
            }
            // Check if it starts with valid digits (not 0 or 1 for US area codes)
            else if (phoneDigits[0] === '0' || phoneDigits[0] === '1') {
                phoneInput.classList.add('error');
                phoneError.classList.add('active');
                phoneError.textContent = 'Phone number cannot start with 0 or 1';
                isValid = false;
            }
            // Check for invalid patterns (all same digit)
            else if (/^(\d)\1{9,}$/.test(phoneDigits)) {
                phoneInput.classList.add('error');
                phoneError.classList.add('active');
                phoneError.textContent = 'Please enter a valid phone number';
                isValid = false;
            }
            
            return isValid;
        }

        // Submit order with customer information
        function submitOrderWithCustomerInfo() {
            // Validate customer information
            if (!validateCustomerInfo()) {
                return;
            }
            
            const customerName = document.getElementById('customerName').value.trim();
            const customerPhone = document.getElementById('customerPhone').value.trim();
            
            // Create order summary for confirmation
            let orderSummary = 'ORDER SUMMARY\n' + '='.repeat(40) + '\n\n';
            orderSummary += 'Customer: ' + customerName + '\n';
            orderSummary += 'Phone: ' + customerPhone + '\n';
            orderSummary += '='.repeat(40) + '\n\n';
            
            let totalItems = 0;
            let totalPrice = 0;
            
            for (let productId in cart) {
                const item = cart[productId];
                const itemTotal = item.quantity * item.price;
                orderSummary += item.name + '\n';
                orderSummary += '  Quantity: ' + item.quantity + ' x $' + item.price.toFixed(2) + '\n';
                orderSummary += '  Subtotal: $' + itemTotal.toFixed(2) + '\n';
                orderSummary += '-'.repeat(40) + '\n';
                
                totalItems += item.quantity;
                totalPrice += itemTotal;
            }
            
            orderSummary += '\nTOTAL ITEMS: ' + totalItems + '\n';
            orderSummary += 'TOTAL PRICE: $' + totalPrice.toFixed(2) + '\n';
            
            // Close modal and show confirmation
            closeCustomerModal();
            
            if (confirm(orderSummary + '\n\nProceed with this order?')) {
                // Submit order to server with customer info
                submitOrder(customerName, customerPhone);
            }
        }

        // Submit order to server
        function submitOrder(customerName, customerPhone) {
            // Prepare order data
            const orderData = {
                items: [],
                timestamp: new Date().toISOString(),
                customer_name: customerName,
                customer_phone: customerPhone
            };
            
            for (let productId in cart) {
                orderData.items.push({
                    product_id: parseInt(productId),
                    quantity: cart[productId].quantity,
                    name: cart[productId].name,
                    price: cart[productId].price
                });
            }
            
            // Create a form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'process-order.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'order_data';
            input.value = JSON.stringify(orderData);
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateOrderCount();
            
            // Add phone formatting listener
            const phoneInput = document.getElementById('customerPhone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    formatPhoneNumber(this);
                });
            }
        });
    </script>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>