-- Electronics & Gadget Store Database Schema
CREATE DATABASE IF NOT EXISTS `electronics_store_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `electronics_store_db`;

-- Drop tables in order of foreign key dependency if re-installing
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `return_requests`;
DROP TABLE IF EXISTS `warranties`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_serials`;
DROP TABLE IF EXISTS `product_specifications`;
DROP TABLE IF EXISTS `stock_logs`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
SET FOREIGN_KEY_CHECKS = 1;

-- Categories Table
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'cpu',
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `brand` VARCHAR(100) NOT NULL,
    `model` VARCHAR(100) NOT NULL,
    `sku` VARCHAR(100) NOT NULL UNIQUE,
    `price` DECIMAL(10,2) NOT NULL,
    `sale_price` DECIMAL(10,2) NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `low_stock_threshold` INT NOT NULL DEFAULT 5,
    `is_featured` TINYINT(1) DEFAULT 0,
    `warranty_months` INT NOT NULL DEFAULT 12,
    `has_serial_tracking` TINYINT(1) DEFAULT 1,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `description` TEXT,
    `rating` DECIMAL(2,1) DEFAULT 4.8,
    `review_count` INT DEFAULT 24,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product Specifications Table
CREATE TABLE `product_specifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `spec_group` VARCHAR(100) NOT NULL DEFAULT 'General',
    `spec_name` VARCHAR(100) NOT NULL,
    `spec_value` VARCHAR(255) NOT NULL,
    `sort_order` INT DEFAULT 0,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_product_spec` (`product_id`, `spec_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product Serials (Individual hardware unit tracking)
CREATE TABLE `product_serials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `serial_number` VARCHAR(100) NOT NULL UNIQUE,
    `status` ENUM('in_stock', 'reserved', 'sold', 'returned_defective', 'refurbished') DEFAULT 'in_stock',
    `order_id` INT NULL,
    `order_item_id` INT NULL,
    `sold_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_serial_status` (`status`),
    INDEX `idx_serial_num` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(50) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `postal_code` VARCHAR(20) NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `tax_amount` DECIMAL(10,2) NOT NULL,
    `shipping_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'Credit Card',
    `payment_status` ENUM('Paid', 'Pending', 'Refunded', 'Failed') DEFAULT 'Paid',
    `order_status` ENUM('Processing', 'Completed', 'Returned', 'Cancelled') DEFAULT 'Processing',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `assigned_serial_numbers` TEXT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Warranty Registration Table
CREATE TABLE `warranties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `warranty_code` VARCHAR(50) NOT NULL UNIQUE,
    `product_id` INT NOT NULL,
    `serial_number` VARCHAR(100) NOT NULL,
    `order_id` INT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(50) NOT NULL,
    `purchase_date` DATE NOT NULL,
    `expiry_date` DATE NOT NULL,
    `warranty_type` VARCHAR(100) DEFAULT 'Standard Manufacturer (1 Year)',
    `status` ENUM('Active', 'Expired', 'Claimed', 'Voided') DEFAULT 'Active',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
    INDEX `idx_warranty_serial` (`serial_number`),
    INDEX `idx_warranty_code` (`warranty_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Return & Replacement Requests (RMA)
CREATE TABLE `return_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rma_number` VARCHAR(50) NOT NULL UNIQUE,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `serial_number` VARCHAR(100) NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `request_type` ENUM('Replacement', 'Refund', 'Store Credit') NOT NULL DEFAULT 'Replacement',
    `reason` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `photo_url` VARCHAR(500) NULL,
    `status` ENUM('Pending Review', 'Approved - Ship Item Back', 'Item Received & Inspected', 'Replacement Dispatched', 'Refund Processed', 'Rejected') DEFAULT 'Pending Review',
    `resolution_notes` TEXT NULL,
    `admin_response` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT,
    INDEX `idx_rma_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoices Table
CREATE TABLE `invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
    `order_id` INT NOT NULL,
    `issue_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `customer_name` VARCHAR(150) NOT NULL,
    `customer_email` VARCHAR(150) NOT NULL,
    `customer_phone` VARCHAR(50) NOT NULL,
    `billing_address` TEXT NOT NULL,
    `subtotal` DECIMAL(10,2) NOT NULL,
    `tax_rate` DECIMAL(5,2) NOT NULL DEFAULT 18.00,
    `tax_amount` DECIMAL(10,2) NOT NULL,
    `shipping_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `payment_status` ENUM('Paid', 'Pending', 'Overdue', 'Refunded') DEFAULT 'Paid',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    INDEX `idx_invoice_num` (`invoice_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stock Movement Logs (Audit Trail)
CREATE TABLE `stock_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `change_type` ENUM('Initial Stock', 'Order Deduction', 'Restock', 'RMA Restock', 'Defective Scrap', 'Manual Adjustment') NOT NULL,
    `quantity_change` INT NOT NULL,
    `previous_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `reference_id` VARCHAR(100) NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_stock_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
