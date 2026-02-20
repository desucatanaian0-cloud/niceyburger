-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 02:39 AM
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
-- Database: `burger_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Burgers'),
(4, 'Combo'),
(2, 'Drinks'),
(3, 'Fries');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(6, 1, 1, '2026-02-19 08:35:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `order_date`) VALUES
(1, 1, 83.93, 'pending', '2026-02-19 04:45:46'),
(2, 2, 139.96, 'pending', '2026-02-19 05:58:49'),
(3, 1, 65.98, 'pending', '2026-02-19 06:19:08'),
(4, 1, 82.78, 'pending', '2026-02-19 08:17:08'),
(5, 1, 48.00, 'cancelled', '2026-02-19 08:29:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 4, 8.99),
(2, 1, 2, 1, 7.99),
(4, 2, 1, 3, 8.99),
(5, 2, 2, 1, 7.99),
(6, 2, 9, 1, 50.00),
(7, 2, 10, 1, 55.00),
(8, 3, 2, 2, 7.99),
(9, 3, 9, 1, 50.00),
(10, 4, 5, 7, 3.49),
(11, 4, 17, 9, 3.49),
(12, 4, 19, 6, 4.49),
(13, 5, 1, 1, 48.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `image`, `is_available`) VALUES
(1, 1, 'Beef Burger', 'Juicy beef patty with cheese and lettuce (buy 1 take 1)', 48.00, 'beefburger.jpg', 1),
(2, 1, 'Chicken Burger', 'Crispy chicken breast with spicy mayo', 7.99, '1771478664_chickenburger.jpg', 1),
(3, 2, 'Coca Cola', 'Chilled 330ml can', 1.99, 'cola.png', 1),
(4, 2, 'Orange Juice', 'Freshly squeezed oranges', 2.99, '1771479944_lemonade.jpg', 1),
(5, 3, 'French Fries', 'Crispy golden potato fries', 3.49, 'fries.png', 1),
(9, 1, 'Bacon Cheeseburger', 'Classic beef patty with crispy bacon and cheddar', 50.00, '1771479651_chese.jpg', 1),
(10, 1, 'Veggie Burger', 'Plant-based patty with avocado and sprouts', 55.00, '1771479764_vegie.jpg', 1),
(11, 1, 'Mushroom Swiss', 'Beef patty topped with sautéed mushrooms and swiss cheese', 9.99, '1771479852_mus.jpg', 1),
(12, 2, 'Coca Cola', 'Chilled 330ml can', 1.99, 'cola.png', 1),
(13, 2, 'Orange Juice', 'Freshly squeezed oranges', 2.99, 'juice.png', 1),
(14, 2, 'Strawberry Milkshake', 'Creamy shake made with real strawberries', 4.49, 'shake1.png', 1),
(15, 2, 'Iced Coffee', 'Cold brew with a splash of milk', 3.99, 'coffee.png', 1),
(16, 2, 'Lemonade', 'Classic homemade style lemonade', 2.49, 'lemonade.png', 1),
(17, 3, 'French Fries', 'Crispy golden potato fries', 3.49, 'fries.png', 1),
(18, 3, 'Cheese Fries', 'Fries smothered in melted cheddar cheese', 4.99, 'fries2.png', 1),
(19, 3, 'Sweet Potato Fries', 'Fried sweet potato strips with cinnamon sugar', 4.49, 'fries3.png', 1),
(20, 3, 'Onion Rings', 'Crispy battered onion rings', 3.99, 'rings.png', 1),
(21, 4, 'Family Combo', 'Burger King 2 Whopper Sandwiches, 2 Original Chicken Sandwiches, 16 piece Chicken Nuggets, 2 Small Fries, and 2 Small Soft Drinks – plenty to share!', 999.00, '1771479330_family combo.jpg', 1),
(22, 4, 'Solo Combo', '1 Burger, 1 Fries, 1 Drink', 12.99, '1771479489_d.jpg', 1),
(23, 4, 'Couple Combo', '2 Burgers, 1 Large Fries, 2 Drinks', 22.99, '1771479435_ee.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `address` text DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `address`, `payment_method`, `profile_image`, `created_at`) VALUES
(1, 'desucatanaian0@gmail.com', 'desucatanaian0@gmail.com', '$2y$10$hmyACh8hmhsiU1IY8Phef.34nIvyn7p7i2f9Lf8tEb1N97xRr7uu2', 'user', 'GK, Tugas, Madridejos', 'G-Cash', '1771484855_tasty-burger-app-design-UI-tubik.png', '2026-02-19 04:38:01'),
(2, 'admin', 'admin123@gmail.com', '$2y$10$lb/rDNBNp/rlyA0/CAnbGOk8y6gVKpEIq/kgNGTn83/IpfAPZIuwO', 'admin', NULL, NULL, NULL, '2026-02-19 05:17:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
