-- Seed data for Electronics & Gadget Store
USE `electronics_store_db`;

-- 1. Insert Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Smartphones & Mobile', 'smartphones', 'smartphone', 'Flagship 5G phones, foldable devices, and camera powerhouses with official warranty.'),
(2, 'Laptops & Ultrabooks', 'laptops', 'laptop', 'High performance creator, gaming, and business laptops with latest processors.'),
(3, 'Audio & Headphones', 'audio', 'headphones', 'Active noise cancellation headphones, audiophile gear, and true wireless earbuds.'),
(4, 'Smartwatches & Wearables', 'wearables', 'watch', 'Rugged outdoor smartwatches, health trackers, and lifestyle wearables.'),
(5, 'Tablets & Displays', 'tablets-displays', 'tablet', 'Productivity tablets, stylus pens, and gaming OLED monitors.'),
(6, 'Accessories & Power', 'accessories', 'zap', 'Fast GaN chargers, premium cables, wireless mice, and protective gear.');

-- 2. Insert Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `brand`, `model`, `sku`, `price`, `sale_price`, `stock_quantity`, `low_stock_threshold`, `is_featured`, `warranty_months`, `has_serial_tracking`, `image_url`, `description`, `rating`, `review_count`) VALUES
(1, 1, 'Apple iPhone 15 Pro Max', 'apple-iphone-15-pro-max', 'Apple', 'A2849', 'APL-IP15PM-256-BLU', 1199.00, 1149.00, 8, 5, 1, 12, 1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80', 'Titanium design with textured matte glass back, Action button, A17 Pro chip with 6-core GPU, 48MP main camera with 5x telephoto optical zoom, and USB-C with USB 3 speeds.', 4.9, 142),
(2, 1, 'Samsung Galaxy S24 Ultra 5G', 'samsung-galaxy-s24-ultra', 'Samsung', 'SM-S928B', 'SAM-S24U-512-GRY', 1299.00, 1219.00, 4, 5, 1, 24, 1, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80', 'Galaxy AI built-in with Live Translate, Note Assist, Circle to Search. 200MP quad camera system, Corning Gorilla Armor anti-reflective display, embedded S Pen, and Snapdragon 8 Gen 3 for Galaxy.', 4.8, 98),
(3, 1, 'Google Pixel 8 Pro', 'google-pixel-8-pro', 'Google', 'GC3VE', 'GOO-PX8P-128-OBS', 999.00, 849.00, 3, 5, 1, 12, 1, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80', 'Powered by Google Tensor G3, leading computational photography with Best Take and Magic Editor, temperature sensor, and 7 years of Android OS and security upgrades.', 4.7, 64),
(4, 1, 'OnePlus 12 5G', 'oneplus-12-5g', 'OnePlus', 'CPH2573', 'OP-12-256-GRN', 799.00, 749.00, 12, 5, 0, 12, 1, 'https://images.unsplash.com/photo-1567581935884-3349723552ca?auto=format&fit=crop&w=800&q=80', 'Snapdragon 8 Gen 3, 4th Gen Hasselblad Camera System with 64MP periscope telephoto, 2K 120Hz ProXDR display with Aqua Touch, and lightning 100W wired charging.', 4.7, 53),
(5, 2, 'MacBook Pro 16" M3 Max', 'macbook-pro-16-m3-max', 'Apple', 'A2991', 'APL-MBP16-M3M-1TB', 3499.00, 3349.00, 2, 3, 1, 12, 1, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80', '16-core CPU, 40-core GPU, 36GB Unified Memory, 1TB ultra-fast SSD storage. Liquid Retina XDR display with up to 1600 nits peak brightness, up to 22 hours battery life.', 4.9, 87),
(6, 2, 'Dell XPS 15 9530 OLED', 'dell-xps-15-9530', 'Dell', 'XPS9530', 'DEL-XPS15-I9-RTX', 2299.00, 2149.00, 6, 4, 1, 24, 1, 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80', 'Intel Core i9-13900H, 32GB DDR5 4800MHz, NVIDIA GeForce RTX 4070 8GB GDDR6, 15.6" 3.5K (3456x2160) OLED Touch InfinityEdge display, CNC machined aluminum chassis.', 4.6, 41),
(7, 2, 'ASUS ROG Zephyrus G14 Gaming Laptop', 'asus-rog-zephyrus-g14', 'ASUS', 'GA403UI', 'ASU-ROG-G14-OLED', 1999.00, 1899.00, 5, 4, 1, 24, 1, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80', 'AMD Ryzen 9 8945HS with Ryzen AI, NVIDIA GeForce RTX 4070 Laptop GPU, 32GB LPDDR5X, 14-inch 3K 120Hz OLED ROG Nebula Display with G-Sync in an ultra-sleek 1.57kg CNC chassis.', 4.8, 39),
(8, 2, 'Lenovo ThinkPad X1 Carbon Gen 11', 'lenovo-thinkpad-x1-carbon-gen-11', 'Lenovo', '21HM000EUS', 'LEN-X1C11-I7-16G', 1649.00, 1520.00, 7, 3, 0, 36, 1, 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80', 'Legendary enterprise ultrabook engineered with Intel vPro on Intel Core i7-1365U, 16GB LPDDR5, 512GB PCIe 4.0 SSD, Military-spec MIL-STD 810H durability, and under 1.12 kg weight.', 4.7, 31),
(9, 3, 'Sony WH-1000XM5 Wireless Noise-Cancelling', 'sony-wh-1000xm5', 'Sony', 'WH1000XM5/B', 'SNY-WH1000XM5-BLK', 399.00, 348.00, 15, 6, 1, 12, 1, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80', 'Two processors and 8 microphones for unprecedented noise cancellation. Auto NC Optimizer, 30mm carbon fiber driver units, 30 hours battery life with quick charge, multipoint Bluetooth 5.2.', 4.8, 210),
(10, 3, 'Apple AirPods Pro (2nd Gen, USB-C)', 'apple-airpods-pro-2-usb-c', 'Apple', 'MTJV3AM/A', 'APL-APP2-USBC', 249.00, 229.00, 22, 10, 1, 12, 1, 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=800&q=80', 'Up to 2x more Active Noise Cancellation, Transparency mode, Adaptive Audio, Personalized Spatial Audio with dynamic head tracking, and MagSafe Charging Case with speaker and lanyard loop.', 4.9, 315),
(11, 4, 'Apple Watch Ultra 2 GPS + Cellular', 'apple-watch-ultra-2', 'Apple', 'A2986', 'APL-AWU2-49TI', 799.00, 779.00, 3, 4, 1, 12, 1, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80', 'The most capable and rugged Apple Watch. Lightweight 49mm aerospace-grade titanium case, 3000 nits display, precision dual-frequency GPS, 36-hour regular battery life, 100m water resistance.', 4.9, 78),
(12, 4, 'Samsung Galaxy Watch 6 Classic 47mm', 'samsung-galaxy-watch-6-classic', 'Samsung', 'SM-R960', 'SAM-GW6C-47-BLK', 399.00, 349.00, 9, 4, 0, 12, 1, 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80', 'Return of the physical rotating bezel, Sapphire Crystal glass, Samsung BioActive Sensor for ECG, blood pressure, BIA body composition, personalized heart rate zones, and Sleep Coaching.', 4.6, 52),
(13, 6, 'Anker 737 GaNPrime 140W Power Bank', 'anker-737-power-bank-140w', 'Anker', 'A1289', 'ANK-737-140W-24K', 149.00, 119.00, 18, 5, 0, 24, 0, 'https://images.unsplash.com/photo-1609592426867-6415a7cf95e7?auto=format&fit=crop&w=800&q=80', 'Equipped with Power Delivery 3.1 and bi-directional charging to rapidly charge or get a 140W ultra-powerful charge. Smart digital display shows output and input wattage and estimated time to fully recharge.', 4.8, 92),
(14, 6, 'Logitech MX Master 3S Performance Mouse', 'logitech-mx-master-3s', 'Logitech', '910-006557', 'LOG-MXM3S-GRAPH', 99.00, 89.00, 25, 5, 0, 24, 0, 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80', 'Any-surface 8K DPI sensor including glass. Quiet Click switches feel satisfying with 90% less noise. MagSpeed electromagnetic scrolling wheel with 1000 lines per second speed and precision.', 4.9, 160);

-- 3. Insert Product Specifications
INSERT INTO `product_specifications` (`product_id`, `spec_group`, `spec_name`, `spec_value`, `sort_order`) VALUES
-- Apple iPhone 15 Pro Max
(1, 'Processor & OS', 'Chipset', 'Apple A17 Pro (3nm)', 1),
(1, 'Processor & OS', 'Operating System', 'iOS 17 (Upgradable to iOS 18)', 2),
(1, 'Memory & Storage', 'RAM', '8GB LPDDR5', 3),
(1, 'Memory & Storage', 'Internal Storage', '256GB NVMe', 4),
(1, 'Memory & Storage', 'Expandable Storage', 'No', 5),
(1, 'Display', 'Screen Size', '6.7 inches', 6),
(1, 'Display', 'Display Type', 'Super Retina XDR OLED, 120Hz ProMotion', 7),
(1, 'Display', 'Resolution', '2796 x 1290 pixels (~460 ppi)', 8),
(1, 'Display', 'Brightness', '2000 nits peak (outdoor)', 9),
(1, 'Camera', 'Rear Cameras', '48MP Main + 12MP Ultra-Wide + 12MP 5x Telephoto', 10),
(1, 'Camera', 'Front Camera', '12MP TrueDepth with Autofocus', 11),
(1, 'Battery & Charging', 'Battery Capacity', '4422 mAh', 12),
(1, 'Battery & Charging', 'Charging Speed', '20W Fast Charging (50% in 30 min), 15W MagSafe', 13),
(1, 'Connectivity & Ports', 'Port Type', 'USB Type-C 3.0 (up to 10Gbps)', 14),
(1, 'Connectivity & Ports', 'Network & 5G', '5G NR, Wi-Fi 6E, Bluetooth 5.3, UWB Gen 2', 15),
(1, 'Build & Warranty', 'Materials', 'Grade 5 Titanium frame, Ceramic Shield front', 16),
(1, 'Build & Warranty', 'Water Resistance', 'IP68 (6m up to 30 mins)', 17),
(1, 'Build & Warranty', 'Weight', '221 grams', 18),
(1, 'Build & Warranty', 'Official Warranty', '12 Months Apple International', 19),

-- Samsung Galaxy S24 Ultra
(2, 'Processor & OS', 'Chipset', 'Qualcomm Snapdragon 8 Gen 3 for Galaxy (4nm)', 1),
(2, 'Processor & OS', 'Operating System', 'Android 14 with One UI 6.1 (7yr updates)', 2),
(2, 'Memory & Storage', 'RAM', '12GB LPDDR5X', 3),
(2, 'Memory & Storage', 'Internal Storage', '512GB UFS 4.0', 4),
(2, 'Memory & Storage', 'Expandable Storage', 'No', 5),
(2, 'Display', 'Screen Size', '6.8 inches', 6),
(2, 'Display', 'Display Type', 'Dynamic LTPO AMOLED 2X, 1-120Hz', 7),
(2, 'Display', 'Resolution', '3120 x 1440 pixels (QHD+)', 8),
(2, 'Display', 'Brightness', '2600 nits peak', 9),
(2, 'Camera', 'Rear Cameras', '200MP Main + 50MP 5x Periscope + 10MP 3x + 12MP Ultra-Wide', 10),
(2, 'Camera', 'Front Camera', '12MP Dual Pixel AF', 11),
(2, 'Battery & Charging', 'Battery Capacity', '5000 mAh', 12),
(2, 'Battery & Charging', 'Charging Speed', '45W Wired (65% in 30 min), 15W Wireless', 13),
(2, 'Connectivity & Ports', 'Port Type', 'USB Type-C 3.2 Gen 1 with DisplayPort', 14),
(2, 'Connectivity & Ports', 'Network & 5G', '5G mmWave/Sub6, Wi-Fi 7, Bluetooth 5.3', 15),
(2, 'Build & Warranty', 'Materials', 'Titanium frame, Gorilla Glass Armor front', 16),
(2, 'Build & Warranty', 'Water Resistance', 'IP68 dust/water resistant (1.5m 30 mins)', 17),
(2, 'Build & Warranty', 'Weight', '232 grams', 18),
(2, 'Build & Warranty', 'Official Warranty', '24 Months Samsung Care Covered', 19),

-- Google Pixel 8 Pro
(3, 'Processor & OS', 'Chipset', 'Google Tensor G3 (4nm) with Titan M2', 1),
(3, 'Processor & OS', 'Operating System', 'Android 14 (7 Years Guaranteed OS Updates)', 2),
(3, 'Memory & Storage', 'RAM', '12GB LPDDR5X', 3),
(3, 'Memory & Storage', 'Internal Storage', '128GB UFS 3.1', 4),
(3, 'Display', 'Screen Size', '6.7 inches Super Actua LTPO OLED', 6),
(3, 'Display', 'Resolution', '2992 x 1344 pixels (489 ppi)', 8),
(3, 'Display', 'Brightness', '2400 nits peak', 9),
(3, 'Camera', 'Rear Cameras', '50MP Octa PD Main + 48MP Quad PD Ultra-Wide + 48MP 5x Telephoto', 10),
(3, 'Battery & Charging', 'Battery Capacity', '5050 mAh', 12),
(3, 'Battery & Charging', 'Charging Speed', '30W Wired, 23W Wireless', 13),
(3, 'Build & Warranty', 'Official Warranty', '12 Months Google Manufacturer', 19),

-- MacBook Pro 16 M3 Max
(5, 'Processor & OS', 'Chipset', 'Apple M3 Max (16-core CPU, 40-core GPU)', 1),
(5, 'Processor & OS', 'Operating System', 'macOS Sonoma', 2),
(5, 'Memory & Storage', 'RAM', '36GB Unified Memory', 3),
(5, 'Memory & Storage', 'Internal Storage', '1TB High-speed NVMe SSD', 4),
(5, 'Display', 'Screen Size', '16.2 inches', 6),
(5, 'Display', 'Display Type', 'Liquid Retina XDR Mini-LED, 120Hz ProMotion', 7),
(5, 'Display', 'Resolution', '3456 x 2234 pixels (254 ppi)', 8),
(5, 'Display', 'Brightness', '1000 nits sustained, 1600 nits peak HDR', 9),
(5, 'Battery & Charging', 'Battery Capacity', '100 Watt-hour Li-Po', 12),
(5, 'Battery & Charging', 'Charging Speed', '140W USB-C Power Adapter (MagSafe 3)', 13),
(5, 'Connectivity & Ports', 'Port Type', '3x Thunderbolt 4, HDMI 2.1, SDXC slot, MagSafe 3', 14),
(5, 'Build & Warranty', 'Weight', '2.16 kg', 18),
(5, 'Build & Warranty', 'Official Warranty', '12 Months Apple Limited Warranty', 19),

-- Dell XPS 15 9530
(6, 'Processor & OS', 'Chipset', 'Intel Core i9-13900H (14 cores, up to 5.4GHz)', 1),
(6, 'Processor & OS', 'Graphics', 'NVIDIA GeForce RTX 4070 (8GB GDDR6)', 2),
(6, 'Processor & OS', 'Operating System', 'Windows 11 Pro 64-bit', 3),
(6, 'Memory & Storage', 'RAM', '32GB DDR5 4800MHz (2x16GB, Dual-Channel)', 4),
(6, 'Memory & Storage', 'Internal Storage', '1TB M.2 PCIe NVMe SSD', 5),
(6, 'Display', 'Screen Size', '15.6 inches', 6),
(6, 'Display', 'Display Type', '3.5K (3456 x 2160) OLED Touch InfinityEdge', 7),
(6, 'Display', 'Color Gamut', '100% DCI-P3, DisplayHDR 500', 9),
(6, 'Battery & Charging', 'Battery Capacity', '86 Watt-hour integrated', 12),
(6, 'Connectivity & Ports', 'Port Type', '2x Thunderbolt 4, 1x USB-C 3.2 Gen 2, Full-size SD', 14),
(6, 'Build & Warranty', 'Weight', '1.92 kg', 18),
(6, 'Build & Warranty', 'Official Warranty', '24 Months Dell On-site Premium Support', 19),

-- ASUS ROG Zephyrus G14
(7, 'Processor & OS', 'Chipset', 'AMD Ryzen 9 8945HS with AMD Ryzen AI', 1),
(7, 'Processor & OS', 'Graphics', 'NVIDIA GeForce RTX 4070 Laptop GPU 8GB GDDR6', 2),
(7, 'Memory & Storage', 'RAM', '32GB LPDDR5X 6400MHz', 3),
(7, 'Memory & Storage', 'Internal Storage', '1TB PCIe 4.0 NVMe M.2 SSD', 4),
(7, 'Display', 'Screen Size', '14.0 inches', 6),
(7, 'Display', 'Display Type', '3K (2880 x 1800) OLED 120Hz 0.2ms ROG Nebula', 7),
(7, 'Build & Warranty', 'Weight', '1.57 kg', 18),
(7, 'Build & Warranty', 'Official Warranty', '24 Months ASUS Global Warranty', 19),

-- Sony WH-1000XM5
(9, 'Audio & Acoustics', 'Driver Unit', '30mm specially designed carbon fiber composite', 1),
(9, 'Audio & Acoustics', 'Noise Cancelling', 'Dual Processors (V1 + QN1) with 8 Microphones', 2),
(9, 'Audio & Acoustics', 'Hi-Res Audio', 'LDAC, DSEE Extreme, Hi-Res Audio Wireless certified', 3),
(9, 'Battery & Charging', 'Battery Life', 'Up to 30 hours with ANC on, 40 hours with ANC off', 12),
(9, 'Battery & Charging', 'Quick Charge', '3 min charge gives 3 hours playback (USB-PD)', 13),
(9, 'Connectivity & Ports', 'Bluetooth Version', 'Bluetooth 5.2 with Multipoint dual-device pairing', 14),
(9, 'Build & Warranty', 'Weight', '250 grams', 18),
(9, 'Build & Warranty', 'Official Warranty', '12 Months Sony Domestic Warranty', 19),

-- Apple Watch Ultra 2
(11, 'Display & Sensor', 'Screen Size', '49mm Titanium case, Sapphire Crystal flat front', 1),
(11, 'Display & Sensor', 'Brightness', '3000 nits maximum display brightness', 2),
(11, 'Processor & OS', 'Chipset', 'Apple S9 SiP with 64-bit dual-core processor', 3),
(11, 'Battery & Charging', 'Battery Life', 'Up to 36 hours regular use, 72 hours Low Power Mode', 12),
(11, 'Build & Warranty', 'Water Resistance', '100m water resistant, EN13319 dive computer standard', 17),
(11, 'Build & Warranty', 'Official Warranty', '12 Months Apple International', 19);

-- 4. Pre-populated Product Serials
INSERT INTO `product_serials` (`product_id`, `serial_number`, `status`, `order_id`, `sold_at`) VALUES
-- iPhone 15 Pro Max Serials
(1, 'SN-IP15PM-8831092', 'sold', 1, '2026-10-15 10:20:00'),
(1, 'SN-IP15PM-8831093', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831094', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831095', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831096', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831097', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831098', 'in_stock', NULL, NULL),
(1, 'SN-IP15PM-8831099', 'in_stock', NULL, NULL),
-- Galaxy S24 Ultra Serials
(2, 'SN-S24U-9942001', 'sold', 2, '2026-10-18 14:45:00'),
(2, 'SN-S24U-9942002', 'in_stock', NULL, NULL),
(2, 'SN-S24U-9942003', 'in_stock', NULL, NULL),
(2, 'SN-S24U-9942004', 'in_stock', NULL, NULL),
-- Pixel 8 Pro Serials (Low stock)
(3, 'SN-PX8P-7721001', 'in_stock', NULL, NULL),
(3, 'SN-PX8P-7721002', 'in_stock', NULL, NULL),
(3, 'SN-PX8P-7721003', 'in_stock', NULL, NULL),
-- MacBook Pro 16 M3 Max Serials (Low stock: 2)
(5, 'SN-MBP16-3390111', 'sold', 3, '2026-10-21 09:15:00'),
(5, 'SN-MBP16-3390112', 'in_stock', NULL, NULL),
(5, 'SN-MBP16-3390113', 'in_stock', NULL, NULL),
-- Sony WH-1000XM5 Serials
(9, 'SN-XM5-551101', 'sold', 1, '2026-10-15 10:20:00'),
(9, 'SN-XM5-551102', 'in_stock', NULL, NULL),
(9, 'SN-XM5-551103', 'in_stock', NULL, NULL),
(9, 'SN-XM5-551104', 'in_stock', NULL, NULL);

-- 5. Seed Historical Orders
INSERT INTO `orders` (`id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `city`, `postal_code`, `subtotal`, `tax_amount`, `shipping_amount`, `total_amount`, `payment_method`, `payment_status`, `order_status`, `created_at`) VALUES
(1, 'ORD-2026-1001', 'Alexander Vance', 'alex.vance@techcorp.io', '+1 (555) 234-5678', '742 Evergreen Terrace, Suite 4B', 'San Jose, CA', '95112', 1497.00, 269.46, 0.00, 1766.46, 'Credit Card (Visa)', 'Paid', 'Completed', '2026-10-15 10:20:00'),
(2, 'ORD-2026-1002', 'Elena Rostova', 'elena.rostova@designlab.org', '+1 (555) 876-5432', '120 Market St, Floor 8', 'Austin, TX', '78701', 1219.00, 219.42, 15.00, 1453.42, 'PayPal', 'Paid', 'Completed', '2026-10-18 14:45:00'),
(3, 'ORD-2026-1003', 'Marcus Sterling', 'msterling@cyberworks.dev', '+1 (555) 432-8765', '450 Silicon Avenue', 'Seattle, WA', '98101', 3349.00, 602.82, 0.00, 3951.82, 'Bank Transfer / Wire', 'Paid', 'Processing', '2026-10-21 09:15:00');

-- 6. Seed Order Items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `assigned_serial_numbers`) VALUES
(1, 1, 1, 'Apple iPhone 15 Pro Max', 1149.00, 1, 1149.00, 'SN-IP15PM-8831092'),
(2, 1, 9, 'Sony WH-1000XM5 Wireless Noise-Cancelling', 348.00, 1, 348.00, 'SN-XM5-551101'),
(3, 2, 2, 'Samsung Galaxy S24 Ultra 5G', 1219.00, 1, 1219.00, 'SN-S24U-9942001'),
(4, 3, 5, 'MacBook Pro 16" M3 Max', 3349.00, 1, 3349.00, 'SN-MBP16-3390111');

-- 7. Seed Invoices
INSERT INTO `invoices` (`id`, `invoice_number`, `order_id`, `issue_date`, `due_date`, `customer_name`, `customer_email`, `customer_phone`, `billing_address`, `subtotal`, `tax_rate`, `tax_amount`, `shipping_amount`, `total_amount`, `payment_status`, `notes`, `created_at`) VALUES
(1, 'INV-2026-8001', 1, '2026-10-15', '2026-10-15', 'Alexander Vance', 'alex.vance@techcorp.io', '+1 (555) 234-5678', '742 Evergreen Terrace, Suite 4B, San Jose, CA 95112', 1497.00, 18.00, 269.46, 0.00, 1766.46, 'Paid', 'Official Tax Invoice. Valid for warranty registration and hardware proof of purchase.', '2026-10-15 10:20:00'),
(2, 'INV-2026-8002', 2, '2026-10-18', '2026-10-18', 'Elena Rostova', 'elena.rostova@designlab.org', '+1 (555) 876-5432', '120 Market St, Floor 8, Austin, TX 78701', 1219.00, 18.00, 219.42, 15.00, 1453.42, 'Paid', 'Standard Tax Invoice. Includes 24-Month manufacturer warranty coverage on Samsung devices.', '2026-10-18 14:45:00'),
(3, 'INV-2026-8003', 3, '2026-10-21', '2026-10-21', 'Marcus Sterling', 'msterling@cyberworks.dev', '+1 (555) 432-8765', '450 Silicon Avenue, Seattle, WA 98101', 3349.00, 18.00, 602.82, 0.00, 3951.82, 'Paid', 'Enterprise Commercial Invoice. Includes serial verification and priority enterprise support.', '2026-10-21 09:15:00');

-- 8. Seed Warranties
INSERT INTO `warranties` (`id`, `warranty_code`, `product_id`, `serial_number`, `order_id`, `customer_name`, `customer_email`, `customer_phone`, `purchase_date`, `expiry_date`, `warranty_type`, `status`, `notes`) VALUES
(1, 'WAR-2026-88101', 1, 'SN-IP15PM-8831092', 1, 'Alexander Vance', 'alex.vance@techcorp.io', '+1 (555) 234-5678', '2026-10-15', '2027-10-15', 'Standard Manufacturer (1 Year)', 'Active', 'Registered automatically at checkout. Covers hardware defects and battery health below 80%.'),
(2, 'WAR-2026-88102', 9, 'SN-XM5-551101', 1, 'Alexander Vance', 'alex.vance@techcorp.io', '+1 (555) 234-5678', '2026-10-15', '2027-10-15', 'Standard Manufacturer (1 Year)', 'Active', 'Sony authorized coverage.'),
(3, 'WAR-2026-88103', 2, 'SN-S24U-9942001', 2, 'Elena Rostova', 'elena.rostova@designlab.org', '+1 (555) 876-5432', '2026-10-18', '2028-10-18', 'Samsung Care+ Extended (2 Years)', 'Active', 'Includes accidental damage protection and priority screen repair.'),
(4, 'WAR-2026-88104', 5, 'SN-MBP16-3390111', 3, 'Marcus Sterling', 'msterling@cyberworks.dev', '+1 (555) 432-8765', '2026-10-21', '2027-10-21', 'AppleCare+ Protection Plan', 'Active', 'Enterprise workstation coverage with 24/7 technical hotline.');

-- 9. Seed RMA / Return Requests
INSERT INTO `return_requests` (`id`, `rma_number`, `order_id`, `product_id`, `serial_number`, `customer_name`, `customer_email`, `request_type`, `reason`, `description`, `status`, `admin_response`, `created_at`) VALUES
(1, 'RMA-2026-0041', 1, 9, 'SN-XM5-551101', 'Alexander Vance', 'alex.vance@techcorp.io', 'Replacement', 'Screen / Display / Audio static defect', 'Right earcup produces slight buzzing/crackling sound during Active Noise Cancellation in ambient mode.', 'Approved - Ship Item Back', 'Prepaid return shipping label generated. RMA authorized for unit replacement upon inspection.', '2026-10-20 11:30:00'),
(2, 'RMA-2026-0042', 2, 2, 'SN-S24U-9942001', 'Elena Rostova', 'elena.rostova@designlab.org', 'Replacement', 'Damaged during shipping', 'Package arrived with crushed external carton; camera housing has small hairline scratch.', 'Pending Review', 'Our logistics team is reviewing transit photos with the carrier.', '2026-10-24 16:10:00');

-- 10. Seed Stock Audit Logs
INSERT INTO `stock_logs` (`product_id`, `change_type`, `quantity_change`, `previous_stock`, `new_stock`, `reference_id`, `note`) VALUES
(1, 'Initial Stock', 9, 0, 9, 'PO-2026-01', 'Initial warehouse stock received'),
(1, 'Order Deduction', -1, 9, 8, 'ORD-2026-1001', 'Order fulfilled with serial SN-IP15PM-8831092'),
(2, 'Initial Stock', 5, 0, 5, 'PO-2026-01', 'Initial shipment from Samsung Global'),
(2, 'Order Deduction', -1, 5, 4, 'ORD-2026-1002', 'Order fulfilled with serial SN-S24U-9942001'),
(5, 'Initial Stock', 3, 0, 3, 'PO-2026-02', 'Stock intake of M3 Max Workstations'),
(5, 'Order Deduction', -1, 3, 2, 'ORD-2026-1003', 'Order fulfilled with serial SN-MBP16-3390111'),
(3, 'Initial Stock', 3, 0, 3, 'PO-2026-02', 'Low inventory intake for Pixel 8 Pro');
