<?php
/*******************************************************
 * @package  wdv341_final_project - Admin Module
 * @author   Hunter Lovan
 * @version  1.0.0
 * @link     http://kickshunter.com/WDV341/index_v1.php
 * *****************************************************
 * ProductModel.php - Model for product data access
 * Handles all database operations for products (Model in MVC)
 * ToDo: move the admin module to its own folder.
 */

class ProductModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get all products excluding first 6
     * @return array Array of product records
     */
    public function getAllProducts() {
        try {
            $sql = "SELECT * FROM wdv341_products WHERE product_id > 6 ORDER BY product_name DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get product by ID
     * @param int $productId
     * @return array|null Product record or null if not found
     */
    public function getProductById($productId) {
        try {
            $sql = "SELECT * FROM wdv341_products WHERE product_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (Throwable $e) {
            error_log("Error fetching product: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if product has sufficient stock
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function hasStock($productId, $quantity) {
        $product = $this->getProductById($productId);
        return $product && $product['product_inStock'] >= $quantity;
    }
    
    /**
     * Get products by category
     * @param string $category
     * @return array
     */
    public function getProductsByCategory($category) {
        try {
            $sql = "SELECT * FROM wdv341_products WHERE product_category = :category AND product_id > 6 ORDER BY product_name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':category', $category);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log("Error fetching products by category: " . $e->getMessage());
            return [];
        }
    }
}
