-- Clean schema for wdv341_Products, wdv341_Categories, Users, and Messages
-- Engine: InnoDB, Charset: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if present (observe FK order) - COMMENTING OUT THE DROP COMMANDS SO WE DON'T ACCIDENTLY RUN THIS 
--DROP TABLE IF EXISTS wdv341_order_items
--DROP TABLE IF EXISTS wdv341_orders
--DROP TABLE IF EXISTS wdv341_products;
--DROP TABLE IF EXISTS wdv341_categories;
--DROP TABLE IF EXISTS messages;
--DROP TABLE IF EXISTS users;

-- 1. Categories Table (create first for FK dependency)(future use or drop it)
CREATE TABLE wdv341_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description TEXT,
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Products Table (no references to categories at this time, bc it will be easier to hard-code in the category for the demo)
CREATE TABLE IF NOT EXISTS `wdv341_products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(250) NOT NULL,
  `product_description` varchar(500) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_image` varchar(250) NOT NULL,
  `product_inStock` int NOT NULL,
  `product_status` varchar(250) NOT NULL,
  `product_update_date` date NOT NULL,
  `product_ingredients` varchar(500) NOT NULL,
  `product_expired_date` date NOT NULL,
  `product_category` varchar(50) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 3. Users Table (Admin)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Messages Table (Contact Form) (future use or drop it)
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data
-- 1) Default admin user (password: admin123)
--INSERT INTO users (username, password, email)
--VALUES ('admin', '123', 'admin@example.com');

-- 2) Categories (future use or drop it)
INSERT INTO categories (name, description) VALUES
  ('Cakes', 'Delicious baked goods, which is bigger than cookies'),
  ('Pastries', 'Flaky and sweet baked treats'),
  ('Breads', 'Freshly baked bread varieties'),
  ('Beverages', 'Hot and cold drinks to accompany your treats');

-- 3) wdv341_Products (mapped to categories above)
--
-- Seed data for table `wdv341_products`
--

INSERT INTO `wdv341_products` (`product_id`, `product_name`, `product_description`, `product_price`, `product_image`, `product_inStock`, `product_status`, `product_update_date`, `product_ingredients`, `product_expired_date`, `product_category`) VALUES
(7, 'Pineapple Cake', 'Pineapple', 3.50, 'pineapplecake.jpg', 98, '', '0000-00-00', '', '0000-00-00', ''),
(8, 'Zuquini bread', 'Moist nostalgic and a Thanksgiving favorite.', 4.25, 'zuquinibread.jpg', 100, 'active', '0000-00-00', '', '0000-00-00', ''),
(9, 'Pumpkin pie', 'Orange delight, boasting a soft, moist, and fluffy', 3.25, 'pumpkinpie1.jpg', 100, '', '0000-00-00', '', '0000-00-00', ''),
(10, 'Strawberry Zest Cupcake', 'Fluffy strawberry sponge with tangy butter cream', 2.25, 'cakeX.jpg', 100, 'active', '0000-00-00', '', '0000-00-00', ''),
(11, 'Caramel Cookie', 'Soft and chocolate butter cream', 1.25, 'caramelChocolate.jpg', 98, 'active', '0000-00-00', '', '0000-00-00', ''),
(12, 'test cookie', 'delete cookie', 1.00, 'cakeX.jpg', 10, 'New', '0000-00-00', '', '0000-00-00', '');
COMMIT;

--
-- Table structure for table `wdv341_orders`
--

CREATE TABLE IF NOT EXISTS `wdv341_orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmation_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `confirmation_number` (`confirmation_number`),
  KEY `idx_confirmation` (`confirmation_number`),
  KEY `idx_customer_phone` (`customer_phone`),
  KEY `idx_order_date` (`order_date`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Seed data for table `wdv341_orders`
--

INSERT INTO `wdv341_orders` (`order_id`, `customer_name`, `customer_phone`, `order_date`, `confirmation_number`, `user_id`, `order_total`, `order_status`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', '555-1234', '2025-11-30 13:52:55', 'ORD-2025-001', NULL, 45.98, 'pending', '2025-11-30 13:52:55', '2025-11-30 13:52:55'),
(2, 'Jane Smith', '555-5678', '2025-11-30 13:52:55', 'ORD-2025-002', NULL, 89.99, 'completed', '2025-11-30 13:52:55', '2025-11-30 13:52:55'),
(3, 'Guest Customer', '555-0000', '2025-11-30 14:21:37', 'ORD-20251130-1B1F1B', NULL, 3.50, 'pending', '2025-11-30 14:21:37', '2025-11-30 14:21:37'),
(4, 'Guest Customer', '555-0000', '2025-11-30 15:53:55', 'ORD-20251130-3A6024', NULL, 3.50, 'pending', '2025-11-30 15:53:55', '2025-11-30 15:53:55'),
(5, 'jimmy john', '(244) 444-4224', '2025-12-02 02:57:22', 'ORD-20251202-2C0B45', NULL, 3.50, 'pending', '2025-12-02 02:57:22', '2025-12-02 02:57:22'),
(6, 'joe smith', '(524) 424-2425', '2025-12-02 04:07:25', 'ORD-20251202-D69A2A', NULL, 1.25, 'pending', '2025-12-02 04:07:25', '2025-12-02 04:07:25'),
(7, 'harper lee', '(423) 422-3424', '2025-12-06 19:22:00', 'ORD-20251206-848F39', NULL, 4.75, 'pending', '2025-12-06 19:22:00', '2025-12-06 19:22:00');
COMMIT;

-- --------------------------------------------------------

--
-- Table structure for table `wdv341_order_items`
--

CREATE TABLE IF NOT EXISTS `wdv341_order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wdv341_order_items`
--

INSERT INTO `wdv341_order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`, `item_total`, `created_at`) VALUES
(1, 1, 7, 2, 12.99, 25.98, '2025-11-30 13:52:55'),
(2, 1, 8, 1, 19.99, 19.99, '2025-11-30 13:52:55'),
(3, 2, 9, 3, 29.99, 89.97, '2025-11-30 13:52:55'),
(4, 3, 7, 1, 3.50, 3.50, '2025-11-30 14:21:37'),
(5, 4, 7, 1, 3.50, 3.50, '2025-11-30 15:53:55'),
(6, 5, 7, 1, 3.50, 3.50, '2025-12-02 02:57:22'),
(7, 6, 11, 1, 1.25, 1.25, '2025-12-02 04:07:25'),
(8, 7, 7, 1, 3.50, 3.50, '2025-12-06 19:22:00'),
(9, 7, 11, 1, 1.25, 1.25, '2025-12-06 19:22:00');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wdv341_order_items`
--
ALTER TABLE `wdv341_order_items`
  ADD CONSTRAINT `wdv341_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `wdv341_orders` (`order_id`) ON DELETE CASCADE;
COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

