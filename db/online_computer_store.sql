-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2025 at 02:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_computer_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(3, 3, 5, 1, '2025-07-15 18:31:42', '2025-07-15 18:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Laptops', 'Portable computers for work and gaming', '2025-07-24 22:06:35'),
(2, 'Desktops', 'Powerful stationary computers', '2025-07-24 22:06:35'),
(3, 'Accessories', 'Keyboards, mice, and other peripherals', '2025-07-24 22:06:35'),
(5, 'Memory card ', '', '2025-07-24 22:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `payment_method`, `shipping_address`, `created_at`, `updated_at`) VALUES
(1, 2, 259.98, 'delivered', 'credit_card', '123 Main St, Anytown, USA', '2025-07-15 18:31:42', '2025-07-15 18:31:42'),
(2, 3, 129.99, 'processing', 'paypal', '456 Oak Ave, Somewhere, USA', '2025-07-15 18:31:42', '2025-07-15 18:31:42'),
(3, 2, 11000.00, 'pending', 'PayPal', '26 Cyclone Trai lBrampton, ON L7A 0A9', '2025-07-18 03:25:08', '2025-07-18 03:25:08'),
(4, 2, 2000.00, 'pending', 'Bank Transfer', '36 Cyclone Trail', '2025-07-22 22:28:53', '2025-07-22 22:28:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 5, 2, 59.99),
(2, 2, 7, 1, 129.99),
(3, 3, 1, 1, 5000.00),
(4, 3, 3, 3, 2000.00),
(5, 4, 3, 1, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` enum('laptop','desktop','accessory','component') NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `brand` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`, `stock`, `brand`, `rating`, `created_at`, `updated_at`, `category_id`) VALUES
(1, 'Premium Laptop Pro X1', 'The Premium Laptop Pro X1 by TechBrand delivers cutting-edge performance in a sleek and modern design. Powered by a high-efficiency processor and equipped with a stunning Full HD display, this laptop is perfect for multitasking, creative work, and entertainment. With 14 units in stock, it’s ideal for professionals and students seeking speed, style, and reliability—all at an affordable price.\r\n\r\nKey Features:\r\n\r\n15.6” Full HD Display\r\n\r\nFast SSD Storage for instant boot-up\r\n\r\nLightweight and portable aluminum body\r\n\r\nLong battery life for all-day use\r\n\r\nIdeal for work, school, and streaming\r\n\r\n', 800.00, '6879bd62a14dc_Screenshot 2025-07-17 231940.png', 'laptop', 14, 'TechBrand', 4.50, '2025-07-15 18:31:42', '2025-07-25 06:32:55', 1),
(2, 'Ultra Slim Notebook', '14\" Full HD, Intel Core i7, 16GB RAM, 512GB SSD', 458.99, '68841a6b65e50_pexels-zeleboba-33092502.jpg', 'laptop', 20, 'UltraBooks', 4.20, '2025-07-15 18:31:42', '2025-07-25 23:59:39', 1),
(3, 'Gaming Desktop Extreme1111', 'Intel Core i9, 32GB RAM, 1TB SSD, NVIDIA RTX 4090', 2000.00, '6879bc7f5156e_Screenshot 2025-07-17 231553.png', 'desktop', 4, 'GameMaster', 4.80, '2025-07-24 18:31:42', '2025-07-25 18:47:14', 2),
(5, 'Best Mouse for games ', 'Ergonomic design, 2.4GHz wireless connection', 59.99, '6883fbc50a607_oscar-ivan-esquivel-arteaga-ZtxED1cpB1E-unsplash.jpg', 'accessory', 50, 'Peripherals+', 4.30, '2025-07-15 18:31:42', '2025-07-25 21:48:53', 3),
(6, '27\" 4K Monitor', 'IPS panel, 99% sRGB, HDR support', 399.99, '6879bd0b27d9f_Screenshot 2025-07-17 231724.png', 'accessory', 25, 'DisplayPro', 4.60, '2025-07-15 18:31:42', '2025-07-25 06:33:19', 1),
(7, 'Mechanical Gaming Keyboard', 'RGB backlight, Cherry MX switches', 129.99, '687aa9cf07478_Screenshot 2025-07-18 160829.png', 'accessory', 18, 'GameGear', 4.40, '2025-07-15 18:31:42', '2025-07-25 06:35:45', 2),
(8, 'ACEMAGIC Laptop Computer Intel N97 Windows', 'Acemagic laptop computers compact design makes it easy to carry with you wherever you go. Also, you can enjoy the benefits of a powerful laptop without the bulk of a traditional desktop. acemagic laptop are built with high-quality components and designed to handle heavy workloads and deliver consistent performance and longevity. We offer lifetime technical support and 12 months repair for customers', 1800.00, '6879bbc8469e0_Screenshot 2025-07-17 230828.png', 'laptop', 40, 'StorageTech', 4.70, '2025-07-24 18:31:42', '2025-07-25 19:26:42', 1),
(14, 'Keyboard ', 'Sony combo ', 1600.00, '6883d6e03f7f6_davide-boscolo-gz9njd0zYbQ-unsplash.jpg', 'laptop', 50, 'Sony ', 0.00, '2025-07-23 19:10:53', '2025-07-25 19:15:33', 3),
(15, 'Desktop premium quality ', 'High tech desktop', 1600.00, '6883d8beeb276_sora-sagano-wQHWqddS_0Q-unsplash.jpg', 'laptop', 50, 'UltraBooks', 0.00, '2025-07-25 19:19:26', '2025-07-25 19:26:23', 2),
(16, 'Keyboard ', 'Keyboard', 1500.00, '6883f6b664980_top-view-smartphone-with-keyboard-charger.jpg', 'laptop', 5, 'StorageTech', 0.00, '2025-07-25 21:27:18', '2025-07-25 21:27:18', 3),
(17, 'Keyboard ', 'Best ', 6000.00, '6883fa3c9f916_sebastian-bednarek-Lxcn2wrM6UY-unsplash.jpg', 'laptop', 45, 'Sony ', 0.00, '2025-07-25 21:33:06', '2025-07-25 21:42:20', 3),
(18, 'SanDisk Extreme Pro 128GB SD Card', 'Speed: Up to 150MB/s\r\n\r\nClass: UHS-II, U3, V30\r\n\r\nIdeal for: 4K video, high-resolution photography\r\n\r\nCompact and durable for demanding workflows.', 500.00, '6883fb2f8a414_erik-mclean-4zt8LdAodmI-unsplash.jpg', 'laptop', 55, 'SanDisk ', 0.00, '2025-07-25 21:44:40', '2025-07-25 21:47:04', 5),
(19, 'Gaming Mouse with RGB Lighting', 'Ultra-lightweight with precision sensor\r\n\r\nSmooth glide and customizable lighting\r\n\r\nA must-have for gamers and digital creators.\r\n\r\n', 8000.00, '6883fb8f412c4_rebekah-yip-FwfyVSfUFWs-unsplash.jpg', 'laptop', 85, 'Peripherals+', 0.00, '2025-07-25 21:47:59', '2025-07-25 21:47:59', 3),
(20, 'Samsung 512GB PRO Plus SD Card', 'Engineered for professionals, the Samsung 512GB PRO Plus SD card offers ultra-fast read speeds of up to 180MB/s and write speeds up to 130MB/s. It supports UHS-I, U3, and V30 standards, making it ideal for 4K UHD video, high-resolution photography, and rapid file transfers. Built for durability and reliability under pressure.', 8500.00, '688405c035a2b_samsung-memory-qTl9LjV6Gz0-unsplash.jpg', 'laptop', 85, 'Samsung', 0.00, '2025-06-01 22:24:53', '2025-07-26 00:03:20', 5),
(21, ' SanDisk & Kingston ', 'Best in all categories. ', 8522.00, '688404ae1eb4c_pexels-fotios-photos-175449.jpg', 'laptop', 85, ' SanDisk', 0.00, '2025-07-25 22:26:54', '2025-07-25 23:56:40', 5),
(22, 'Sd card', 'Top choice for cameras.', 85.00, '688405546a8fe_Screenshot 2025-07-25 182848.png', 'laptop', 88, '', 0.00, '2025-07-25 22:29:40', '2025-07-25 22:29:40', 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'Amazing laptop! Super fast and the display is gorgeous.', '2025-07-15 18:31:42'),
(2, 3, 5, 4, 'Good keyboard and mouse set, works perfectly.', '2025-07-15 18:31:42'),
(3, 2, 3, 5, 'This gaming desktop is a beast! Handles everything I throw at it.', '2025-07-15 18:31:42'),
(4, 2, 3, 4, 'Computer is good', '2025-07-17 21:33:39'),
(5, 2, 3, 1, 'didn\'t like it', '2025-07-18 00:18:03'),
(6, 2, 6, 4, 'it is good', '2025-07-22 22:29:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '2025-07-15 18:31:42', '2025-07-15 18:31:42'),
(2, 'John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-07-15 18:31:42', '2025-07-15 18:31:42'),
(3, 'Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-07-15 18:31:42', '2025-07-15 18:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 2, 6, '2025-07-15 18:31:43'),
(3, 3, 2, '2025-07-15 18:31:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
