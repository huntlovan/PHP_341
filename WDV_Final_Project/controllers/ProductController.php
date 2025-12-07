<?php
/**
 * ProductController.php - Controller for product-related business logic
 * Handles product display, order validation, and order processing
 */

class ProductController {
    private $productModel;
    
    public function __construct($productModel) {
        $this->productModel = $productModel;
    }
    
    /**
     * Get products formatted for display
     * Adds calculated fields and formats data for the view
     * 
     * @return array Products with display formatting
     */
    public function getProductsForDisplay() {
        $products = $this->productModel->getAllProducts();
        $displayProducts = [];
        
        foreach ($products as $product) {
            $inStock = (int)($product['product_inStock'] ?? 0);
            
            $displayProducts[] = [
                'id' => (int)($product['product_id'] ?? 0),
                'name' => htmlspecialchars($product['product_name'] ?? ''),
                'description' => htmlspecialchars($product['product_description'] ?? ''),
                'price' => isset($product['product_price']) ? number_format((float)$product['product_price'], 2) : '0.00',
                'priceRaw' => (float)($product['product_price'] ?? 0),
                'image' => htmlspecialchars($product['product_image'] ?? ''),
                'status' => trim((string)($product['product_status'] ?? '')),
                'inStock' => $inStock,
                'isLowStock' => $inStock < 10,
                'inventoryClass' => 'productInventory' . ($inStock < 10 ? ' productLowInventory' : '')
            ];
        }
        
        return $displayProducts;
    }
    
    /**
     * Validate order data before processing
     * 
     * @param array $orderData Order information including items and customer
     * @return array ['valid' => bool, 'message' => string, 'errors' => array]
     */
    public function validateOrder($orderData) {
        $errors = [];
        
        // Validate customer information
        if (empty($orderData['customer_name']) || strlen(trim($orderData['customer_name'])) < 2) {
            $errors[] = 'Valid customer name is required';
        }
        
        if (empty($orderData['customer_phone'])) {
            $errors[] = 'Customer phone number is required';
        } else {
            $phoneDigits = preg_replace('/\D/', '', $orderData['customer_phone']);
            if (strlen($phoneDigits) < 10) {
                $errors[] = 'Valid phone number is required (minimum 10 digits)';
            }
        }
        
        // Validate items
        if (empty($orderData['items']) || !is_array($orderData['items'])) {
            $errors[] = 'Order must contain at least one item';
        } else {
            foreach ($orderData['items'] as $item) {
                // Validate product exists and has sufficient stock
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    $errors[] = 'Invalid item in order';
                    continue;
                }
                
                $product = $this->productModel->getProductById($item['product_id']);
                if (!$product) {
                    $errors[] = "Product ID {$item['product_id']} not found";
                    continue;
                }
                
                if (!$this->productModel->hasStock($item['product_id'], $item['quantity'])) {
                    $errors[] = "Insufficient stock for {$product['product_name']}";
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'message' => empty($errors) ? 'Order validation passed' : 'Order validation failed',
            'errors' => $errors
        ];
    }
    
    /**
     * Calculate order totals
     * 
     * @param array $items Order items with product_id, quantity
     * @return array ['totalItems' => int, 'totalPrice' => float, 'itemDetails' => array]
     */
    public function calculateOrderTotal($items) {
        $totalItems = 0;
        $totalPrice = 0.0;
        $itemDetails = [];
        
        foreach ($items as $item) {
            $product = $this->productModel->getProductById($item['product_id']);
            if ($product) {
                $quantity = (int)$item['quantity'];
                $price = (float)$product['product_price'];
                $subtotal = $quantity * $price;
                
                $totalItems += $quantity;
                $totalPrice += $subtotal;
                
                $itemDetails[] = [
                    'product_id' => $product['product_id'],
                    'name' => $product['product_name'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }
        }
        
        return [
            'totalItems' => $totalItems,
            'totalPrice' => $totalPrice,
            'itemDetails' => $itemDetails
        ];
    }
    
    /**
     * Process order (placeholder for actual order processing)
     * In a real application, this would save to orders table, update inventory, etc.
     * 
     * @param array $orderData Complete order data
     * @return array ['success' => bool, 'message' => string, 'orderId' => int|null]
     */
    public function processOrder($orderData) {
        // Validate order first
        $validation = $this->validateOrder($orderData);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => implode('; ', $validation['errors']),
                'orderId' => null
            ];
        }
        
        // Calculate totals
        $totals = $this->calculateOrderTotal($orderData['items']);
        
        // In a real application, you would:
        // 1. Start a database transaction
        // 2. Insert into orders table
        // 3. Insert into order_items table
        // 4. Update product inventory
        // 5. Commit transaction
        // 6. Send confirmation email
        
        // For now, just return success
        return [
            'success' => true,
            'message' => 'Order placed successfully',
            'orderId' => rand(1000, 9999), // Mock order ID
            'totals' => $totals,
            'customer' => [
                'name' => $orderData['customer_name'],
                'phone' => $orderData['customer_phone']
            ]
        ];
    }
}
