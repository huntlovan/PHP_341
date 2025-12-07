<?php
/*******************************************************
 * @package  wdv341_final_project - Admin Module
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * updateProducts.php - Display, update, and delete products
 * ToDo: implement the MVC design pattern, and move the admin module to its own folder.
 */
session_start();

// Check if user is logged in
if (!isset($_SESSION['validUser']) || $_SESSION['validUser'] !== true) {
    header('Location: login_v1.php');
    exit;
}

// Set page-specific variables
$pageTitle = 'Manage Products - Mimi\'s Bakery';
$headerTitle = 'Product Management';
$headerSubtitle = 'View, Update, and Delete Products';

// Include database connection
ob_start();
require_once __DIR__ . '/dbConnect1.php';
ob_end_clean();

// Check for success message from insert
$successMessage = '';
if (isset($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    
    try {
        $sql = "DELETE FROM wdv341_products WHERE product_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $productId);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Product has been successfully deleted!';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error deleting product: ' . $e->getMessage();
    }
    
    header('Location: updateProducts.php');
    exit;
}

// Fetch all products
$products = [];
try {
    $sql = "SELECT * FROM wdv341_products WHERE product_id > 6 ORDER BY product_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Error fetching products: ' . $e->getMessage();
}

// Include header
include __DIR__ . '/includes/header.php';
?>
    <style>
        /* Page-specific styles */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            justify-content: center;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #5965af;
            color: white;
        }
        
        .btn-primary:hover {
            background: #484c9b;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .success-message {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #059669;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        
        .products-table {
            width: 100%;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #5965af;
            color: white;
        }
        
        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        tbody tr:hover {
            background: #f9fafb;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .low-stock {
            color: #dc2626;
            font-weight: 600;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-sm {
            padding: 8px 14px;
            font-size: 14px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background: #f59e0b;
            color: white;
        }
        
        .btn-edit:hover {
            background: #d97706;
        }
        
        .btn-delete {
            background: #dc2626;
            color: white;
        }
        
        .btn-delete:hover {
            background: #b91c1c;
        }
        
        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .products-table {
                overflow-x: auto;
            }
            
            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
<?php
// Include navigation and banner
include __DIR__ . '/includes/nav-header.php';
?>

    <div class="container">
        <?php if ($successMessage): ?>
            <div class="success-message">
                ✅ <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <div class="action-buttons">
            <a href="productInputForm.html" class="btn btn-primary">➕ Add New Product</a>
            <a href="login_v1.php" class="btn btn-secondary">← Back to Admin Panel</a>
        </div>
        
        <div class="products-table">
            <?php if (!empty($products)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="productImages/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                         class="product-image">
                                </td>
                                <td><strong><?php echo htmlspecialchars($product['product_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($product['product_description'], 0, 60)) . '...'; ?></td>
                                <td>$<?php echo number_format($product['product_price'], 2); ?></td>
                                <td class="<?php echo $product['product_inStock'] < 10 ? 'low-stock' : ''; ?>">
                                    <?php echo $product['product_inStock']; ?>
                                    <?php if ($product['product_inStock'] < 10): ?>
                                        ⚠️
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($product['product_status'])): ?>
                                        <span class="status-badge"><?php echo htmlspecialchars($product['product_status']); ?></span>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="editProduct.php?id=<?php echo $product['product_id']; ?>" class="btn-sm btn-edit">
                                            ✏️ Edit
                                        </a>
                                        <a href="updateProducts.php?action=delete&id=<?php echo $product['product_id']; ?>" 
                                           class="btn-sm btn-delete"
                                           onclick="return confirm('Are you sure you want to delete this product?')">
                                            🗑️ Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-products">
                    <h3>No products found</h3>
                    <p>Start by adding your first product!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php
// Include footer
include __DIR__ . '/includes/footer.php';
?>
