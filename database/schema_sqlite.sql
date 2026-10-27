-- SQLite Schema for ElectroPulse
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS `stock_logs`;
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `return_requests`;
DROP TABLE IF EXISTS `warranties`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_serials`;
DROP TABLE IF EXISTS `product_specifications`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` TEXT NOT NULL,
    `slug` TEXT NOT NULL UNIQUE,
    `icon` TEXT DEFAULT 'cpu',
    `description` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `products` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `category_id` INTEGER NOT NULL,
    `name` TEXT NOT NULL,
    `slug` TEXT NOT NULL UNIQUE,
    `brand` TEXT NOT NULL,
    `model` TEXT NOT NULL,
    `sku` TEXT NOT NULL UNIQUE,
    `price` REAL NOT NULL,
    `sale_price` REAL NULL,
    `stock_quantity` INTEGER NOT NULL DEFAULT 0,
    `low_stock_threshold` INTEGER NOT NULL DEFAULT 5,
    `is_featured` INTEGER DEFAULT 0,
    `warranty_months` INTEGER NOT NULL DEFAULT 12,
    `has_serial_tracking` INTEGER DEFAULT 1,
    `image_url` TEXT DEFAULT NULL,
    `description` TEXT,
    `rating` REAL DEFAULT 4.8,
    `review_count` INTEGER DEFAULT 24,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
);

CREATE TABLE `product_specifications` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `product_id` INTEGER NOT NULL,
    `spec_group` TEXT NOT NULL DEFAULT 'General',
    `spec_name` TEXT NOT NULL,
    `spec_value` TEXT NOT NULL,
    `sort_order` INTEGER DEFAULT 0,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

CREATE TABLE `product_serials` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `product_id` INTEGER NOT NULL,
    `serial_number` TEXT NOT NULL UNIQUE,
    `status` TEXT DEFAULT 'in_stock',
    `order_id` INTEGER NULL,
    `order_item_id` INTEGER NULL,
    `sold_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

CREATE TABLE `orders` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `order_number` TEXT NOT NULL UNIQUE,
    `customer_name` TEXT NOT NULL,
    `customer_email` TEXT NOT NULL,
    `customer_phone` TEXT NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `city` TEXT NOT NULL,
    `postal_code` TEXT NOT NULL,
    `subtotal` REAL NOT NULL,
    `tax_amount` REAL NOT NULL,
    `shipping_amount` REAL NOT NULL DEFAULT 0.00,
    `total_amount` REAL NOT NULL,
    `payment_method` TEXT DEFAULT 'Credit Card',
    `payment_status` TEXT DEFAULT 'Paid',
    `order_status` TEXT DEFAULT 'Processing',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `order_items` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `order_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `product_name` TEXT NOT NULL,
    `unit_price` REAL NOT NULL,
    `quantity` INTEGER NOT NULL,
    `subtotal` REAL NOT NULL,
    `assigned_serial_numbers` TEXT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
);

CREATE TABLE `warranties` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `warranty_code` TEXT NOT NULL UNIQUE,
    `product_id` INTEGER NOT NULL,
    `serial_number` TEXT NOT NULL,
    `order_id` INTEGER NULL,
    `customer_name` TEXT NOT NULL,
    `customer_email` TEXT NOT NULL,
    `customer_phone` TEXT NOT NULL,
    `purchase_date` DATE NOT NULL,
    `expiry_date` DATE NOT NULL,
    `warranty_type` TEXT DEFAULT 'Standard Manufacturer (1 Year)',
    `status` TEXT DEFAULT 'Active',
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
);

CREATE TABLE `return_requests` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `rma_number` TEXT NOT NULL UNIQUE,
    `order_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `serial_number` TEXT NULL,
    `customer_name` TEXT NOT NULL,
    `customer_email` TEXT NOT NULL,
    `request_type` TEXT NOT NULL DEFAULT 'Replacement',
    `reason` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `photo_url` TEXT NULL,
    `status` TEXT DEFAULT 'Pending Review',
    `resolution_notes` TEXT NULL,
    `admin_response` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
);

CREATE TABLE `invoices` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `invoice_number` TEXT NOT NULL UNIQUE,
    `order_id` INTEGER NOT NULL,
    `issue_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `customer_name` TEXT NOT NULL,
    `customer_email` TEXT NOT NULL,
    `customer_phone` TEXT NOT NULL,
    `billing_address` TEXT NOT NULL,
    `subtotal` REAL NOT NULL,
    `tax_rate` REAL NOT NULL DEFAULT 18.00,
    `tax_amount` REAL NOT NULL,
    `shipping_amount` REAL NOT NULL DEFAULT 0.00,
    `total_amount` REAL NOT NULL,
    `payment_status` TEXT DEFAULT 'Paid',
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
);

CREATE TABLE `stock_logs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `product_id` INTEGER NOT NULL,
    `change_type` TEXT NOT NULL,
    `quantity_change` INTEGER NOT NULL,
    `previous_stock` INTEGER NOT NULL,
    `new_stock` INTEGER NOT NULL,
    `reference_id` TEXT NULL,
    `note` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);
