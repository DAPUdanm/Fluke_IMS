-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 02, 2025 at 05:46 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fluke_ims`
--
CREATE DATABASE IF NOT EXISTS `fluke_ims` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `fluke_ims`;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('Laptop','Desktop','Peripheral Device') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=739 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_name`, `quantity`, `price`, `category`) VALUES
(730, 'Acer Predator XB273K Monitor', 15, 899.00, 'Peripheral Device'),
(728, 'Lenovo ThinkVision P27h Monitor', 25, 499.00, 'Peripheral Device'),
(729, 'Asus ROG Swift PG279Q Monitor', 18, 699.99, 'Peripheral Device'),
(726, 'Dell UltraSharp U2723QE Monitor', 30, 649.99, 'Peripheral Device'),
(727, 'HP 27f 4K Monitor', 27, 379.99, 'Peripheral Device'),
(724, 'Logitech MX Master 3 Mouse', 60, 99.99, 'Peripheral Device'),
(725, 'Corsair K95 RGB Platinum Keyboard', 40, 199.99, 'Peripheral Device'),
(722, 'iBUYPOWER Snowblind', 10, 1299.99, 'Desktop'),
(723, 'Alienware Area-51', 5, 1999.00, 'Desktop'),
(720, 'MSI Trident X', 10, 1499.99, 'Desktop'),
(721, 'CyberPowerPC Gamer Ultra', 25, 899.00, 'Desktop'),
(718, 'Asus VivoPC', 30, 699.99, 'Desktop'),
(719, 'Acer Veriton N', 31, 599.00, 'Desktop'),
(716, 'HP Envy Desktop', 20, 799.99, 'Desktop'),
(717, 'Lenovo Yoga AIO 7', 15, 1599.00, 'Desktop'),
(714, 'Dell G5 Gaming Desktop', 25, 1099.99, 'Desktop'),
(713, 'Alienware X51 R3', 6, 999.00, 'Desktop'),
(711, 'CyberPowerPC Syber C Series', 20, 899.00, 'Desktop'),
(712, 'iBUYPOWER Revolt 3', 11, 1199.99, 'Desktop'),
(709, 'Acer Chromebox CXI4', 37, 499.00, 'Desktop'),
(710, 'MSI Cubi 5', 26, 649.99, 'Desktop'),
(708, 'Asus Mini PC PN50', 35, 599.99, 'Desktop'),
(706, 'HP Z2 G8 Workstation', 14, 1299.99, 'Desktop'),
(707, 'Lenovo ThinkStation P340', 20, 1099.00, 'Desktop'),
(705, 'Apple Mac Studio', 10, 1999.00, 'Desktop'),
(703, 'Alienware Aurora Ryzen Edition', 0, 1599.00, 'Desktop'),
(704, 'Dell Precision 3650', 25, 1199.00, 'Desktop'),
(701, 'CyberPowerPC Gamer Supreme', 15, 1399.99, 'Desktop'),
(702, 'iBUYPOWER Slate MR', 18, 999.99, 'Desktop'),
(699, 'Acer Aspire C27', 35, 799.99, 'Desktop'),
(700, 'MSI Pro DP21', 23, 699.00, 'Desktop'),
(696, 'HP Omen 30L', 20, 1299.99, 'Desktop'),
(697, 'Lenovo Legion T5', 24, 1199.00, 'Desktop'),
(698, 'Asus ProArt Station D940MX', 10, 1499.99, 'Desktop'),
(694, 'Dell XPS Desktop', 35, 999.99, 'Desktop'),
(695, 'Apple iMac 27\"', 11, 1799.00, 'Desktop'),
(692, 'iBUYPOWER Trace 4 MR', 20, 949.99, 'Desktop'),
(693, 'Alienware Aurora R13', -5, 1599.00, 'Desktop'),
(690, 'MSI Aegis SE', 24, 999.99, 'Desktop'),
(691, 'CyberPowerPC Gamer Master', 30, 1099.00, 'Desktop'),
(688, 'Asus ROG Strix GA15', 20, 1299.99, 'Desktop'),
(689, 'Acer Predator Orion 3000', 15, 1199.00, 'Desktop'),
(686, 'HP Pavilion TP01', 38, 599.99, 'Desktop'),
(687, 'Lenovo IdeaCentre 5', 35, 749.00, 'Desktop'),
(684, 'Dell Inspiron 3880', 44, 549.99, 'Desktop'),
(685, 'Apple Mac Mini M2', 29, 699.00, 'Desktop'),
(682, 'iBUYPOWER Element MR 9320', 25, 999.99, 'Desktop'),
(683, 'Alienware Aurora R12', 9, 1499.00, 'Desktop'),
(680, 'MSI MPG Trident 3', 15, 1199.99, 'Desktop'),
(681, 'CyberPowerPC Gamer Xtreme', 20, 1099.00, 'Desktop'),
(678, 'Asus ExpertCenter D7', 35, 749.99, 'Desktop'),
(679, 'Acer Aspire TC-895', 50, 649.99, 'Desktop'),
(676, 'HP EliteDesk 800 G6', 29, 899.99, 'Desktop'),
(677, 'Lenovo ThinkCentre M90a', 20, 999.00, 'Desktop'),
(674, 'Dell OptiPlex 7090', 40, 799.99, 'Desktop'),
(675, 'Apple iMac 24\"', 22, 1299.00, 'Desktop'),
(672, 'Samsung Galaxy Book S', 29, 999.99, 'Laptop'),
(673, 'Dell XPS 15', 18, 1499.00, 'Laptop'),
(670, 'Razer Blade 14', 15, 1999.99, 'Laptop'),
(671, 'LG Gram 15', 25, 1299.00, 'Laptop'),
(669, 'Acer Spin 5', 39, 999.99, 'Laptop'),
(667, 'Asus ExpertBook B9', 20, 1399.99, 'Laptop'),
(668, 'Microsoft Surface Laptop Go 2', 35, 749.00, 'Laptop'),
(665, 'HP Omen 15', 24, 1299.99, 'Laptop'),
(666, 'Lenovo ThinkBook 14s', 18, 899.00, 'Laptop'),
(663, 'Dell Vostro 14', 55, 699.99, 'Laptop'),
(664, 'Apple MacBook Pro M3', 15, 2199.00, 'Laptop'),
(660, 'Razer Blade 17', 10, 2499.99, 'Laptop'),
(661, 'LG Ultra PC 17', 15, 1399.00, 'Laptop'),
(662, 'Samsung Galaxy Book Ion', 19, 1199.99, 'Laptop'),
(658, 'Microsoft Surface Pro 8', 25, 1299.00, 'Laptop'),
(659, 'Acer Nitro 5', 44, 849.99, 'Laptop'),
(656, 'Lenovo Legion 5', 38, 1099.00, 'Laptop'),
(657, 'Asus TUF Dash F15', 35, 999.99, 'Laptop'),
(654, 'Apple MacBook Pro 16\"', 20, 2399.00, 'Laptop'),
(655, 'HP EliteBook 840 G8', 30, 1349.99, 'Laptop'),
(652, 'Samsung Notebook 9 Pro', 25, 1199.99, 'Laptop'),
(653, 'Dell G5 15', 50, 899.99, 'Laptop'),
(651, 'LG Gram 16', 20, 1499.00, 'Laptop'),
(649, 'Acer Chromebook Spin 713', 28, 629.99, 'Laptop'),
(650, 'Razer Blade Stealth 13', 15, 1799.99, 'Laptop'),
(647, 'Asus VivoBook S15', 35, 699.99, 'Laptop'),
(648, 'Microsoft Surface Go 3', 40, 599.00, 'Laptop'),
(645, 'HP Pavilion 14', 60, 649.99, 'Laptop'),
(646, 'Lenovo IdeaPad 5', 54, 749.00, 'Laptop'),
(644, 'Apple MacBook Air M1', 50, 999.00, 'Laptop'),
(642, 'Samsung Galaxy Book Flex', 45, 1399.99, 'Laptop'),
(643, 'Dell Latitude 7420', 25, 1249.00, 'Laptop'),
(640, 'Razer Book 13', 10, 1699.99, 'Laptop'),
(641, 'LG Gram 14', 30, 1299.00, 'Laptop'),
(638, 'Microsoft Surface Laptop Studio', 15, 1599.00, 'Laptop'),
(639, 'Acer Aspire 5', 61, 499.99, 'Laptop'),
(636, 'Lenovo Yoga 9i', 25, 1199.00, 'Laptop'),
(637, 'Asus ROG Zephyrus G14', 20, 1499.99, 'Laptop'),
(634, 'Apple MacBook Pro 14\"', 30, 1999.00, 'Laptop'),
(635, 'HP Envy 13', 38, 849.99, 'Laptop'),
(632, 'Samsung Galaxy Book Pro', 55, 1099.99, 'Laptop'),
(633, 'Dell Inspiron 15', 64, 549.99, 'Laptop'),
(629, 'Acer Swift 5', 39, 749.99, 'Laptop'),
(630, 'Razer Blade 15', 20, 1999.99, 'Laptop'),
(631, 'LG Gram 17', 15, 1699.00, 'Laptop'),
(627, 'Asus ZenBook 14', 40, 899.99, 'Laptop'),
(628, 'Microsoft Surface Laptop 5', 35, 1299.00, 'Laptop'),
(625, 'HP Spectre x360', 30, 1249.99, 'Laptop'),
(626, 'Lenovo ThinkPad X1 Carbon', 25, 1399.00, 'Laptop'),
(623, 'Dell XPS 13', 43, 999.99, 'Laptop'),
(624, 'Apple MacBook Air M2', 59, 1199.00, 'Laptop'),
(733, 'Battery', 23, 5000.00, 'Peripheral Device');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(25) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `product_details` text NOT NULL,
  `invoice_status` enum('completed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_id` (`invoice_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_id`, `order_id`, `invoice_date`, `customer_name`, `contact_number`, `total_amount`, `payment_method`, `product_details`, `invoice_status`, `created_at`) VALUES
(62, 'INVO-20250603-57996', 'ORD-20250603-48225', '2025-06-03', 'janeesha weerasigha', '07658422175', 1599.98, 'Card', '[{\"product_name\":\"Dell XPS 13\",\"quantity\":1,\"unit_price\":\"999.99\",\"amount\":\"999.99\"},{\"product_name\":\"HP Pavilion TP01\",\"quantity\":1,\"unit_price\":\"599.99\",\"amount\":\"599.99\"}]', 'completed', '2025-06-03 16:59:23'),
(63, 'INVO-20250603-97982', 'ORD-20250603-94094', '2025-06-03', 'thilina dishan', '0704789512', 7995.00, 'Card', '[{\"product_name\":\"Alienware Aurora R13\",\"quantity\":5,\"unit_price\":\"1599.00\",\"amount\":\"7995.00\"}]', 'completed', '2025-06-03 17:19:50'),
(61, 'INVO-20250603-31939', 'ORD-20250603-54991', '2025-06-03', 'danm withanage', '0766401959', 629.99, 'Card', '[{\"product_name\":\"Acer Chromebook Spin 713\",\"quantity\":1,\"unit_price\":\"629.99\",\"amount\":\"629.99\"}]', 'completed', '2025-06-03 13:47:36'),
(60, 'INVO-20250603-83381', 'ORD-20250603-99710', '2025-06-03', 'kamal silva', '0741578549', 699.99, 'Bank Transfer', '[{\"product_name\":\"Asus ROG Swift PG279Q Monitor\",\"quantity\":1,\"unit_price\":699.99,\"amount\":699.99}]', 'completed', '2025-06-03 12:50:15'),
(59, 'INVO-20250603-31015', 'ORD-20250603-99710', '2025-06-03', 'kamal silva', '0741578549', 2498.99, 'Bank Transfer', '[{\"product_name\":\"Apple iMac 27\\\"\",\"quantity\":1,\"unit_price\":\"1799.00\",\"amount\":\"1799.00\"},{\"product_name\":\"Asus ROG Swift PG279Q Monitor\",\"quantity\":1,\"unit_price\":\"699.99\",\"amount\":\"699.99\"}]', 'cancelled', '2025-06-03 12:49:58'),
(57, 'INVO-20250603-97927', 'ORD-20250603-69043', '2025-06-03', 'test5', 'test5', 3749.95, 'Bank Transfer', '[{\"product_name\":\"Acer Swift 5\",\"quantity\":5,\"unit_price\":\"749.99\",\"amount\":\"3749.95\"}]', 'cancelled', '2025-06-03 12:07:07'),
(52, 'INVO-20250603-43061', 'ORD-20250603-54326', '2025-06-03', 'test2', 'test2', 499.99, 'Bank Transfer', '[{\"product_name\":\"Acer Aspire 5\",\"quantity\":1,\"unit_price\":\"499.99\",\"amount\":\"499.99\"}]', 'completed', '2025-06-03 10:21:54'),
(54, 'INVO-20250603-50815', 'ORD-20250603-74634', '2025-06-03', 'qwerty', '072 1245789', 699.00, 'Bank Transfer', '[{\"product_name\":\"Apple Mac Mini M2\",\"quantity\":1,\"unit_price\":\"699.00\",\"amount\":\"699.00\"}]', 'cancelled', '2025-06-03 11:59:37'),
(55, 'INVO-20250603-67178', 'ORD-20250603-74634', '2025-06-03', 'qwerty', '072 1245789', 849.99, 'Card', '[{\"product_name\":\"Acer Nitro 5\",\"quantity\":1,\"unit_price\":849.99,\"amount\":849.99}]', 'cancelled', '2025-06-03 12:00:14'),
(58, 'INVO-20250603-74290', 'ORD-20250603-69043', '2025-06-03', 'test5', 'test5', 7499.90, 'Bank Transfer', '[{\"product_name\":\"Acer Swift 5\",\"quantity\":10,\"unit_price\":749.99,\"amount\":7499.9}]', 'cancelled', '2025-06-03 12:07:39'),
(56, 'INVO-20250603-12771', 'ORD-20250603-98798', '2025-06-03', 'test4', 'test4', 749.99, 'Bank Transfer', '[{\"product_name\":\"Acer Swift 5\",\"quantity\":1,\"unit_price\":\"749.99\",\"amount\":\"749.99\"}]', 'cancelled', '2025-06-03 12:02:37'),
(51, 'INVO-20250603-82536', 'ORD-20250603-47997', '2025-06-03', 'danm', '076', 999.00, 'Bank Transfer', '[{\"product_name\":\"Alienware X51 R3\",\"quantity\":1,\"unit_price\":999,\"amount\":999}]', 'cancelled', '2025-06-03 10:13:47'),
(50, 'INVO-20250603-77745', 'ORD-20250603-47997', '2025-06-03', 'test1', 'test1', 799.99, 'Cash', '[{\"product_name\":\"Acer Aspire C27\",\"quantity\":1,\"unit_price\":\"799.99\",\"amount\":\"799.99\"}]', 'cancelled', '2025-06-03 10:13:20'),
(64, 'INVO-20250604-25913', 'ORD-20250604-13591', '2025-06-04', 'ashan sanjula', '0721547893', 1798.98, 'Bank Transfer', '[{\"product_name\":\"Acer Chromebox CXI4\",\"quantity\":1,\"unit_price\":\"499.00\",\"amount\":\"499.00\"},{\"product_name\":\"MSI Cubi 5\",\"quantity\":2,\"unit_price\":\"649.99\",\"amount\":\"1299.98\"}]', 'cancelled', '2025-06-04 06:51:48'),
(65, 'INVO-20250604-49818', 'ORD-20250604-13591', '2025-06-04', 'ashan sanjula', '0721547893', 4097.98, 'Bank Transfer', '[{\"product_name\":\"Apple iMac 24\\\"\",\"quantity\":1,\"unit_price\":1299,\"amount\":1299},{\"product_name\":\"MSI Cubi 5\",\"quantity\":2,\"unit_price\":649.99,\"amount\":1299.98},{\"product_name\":\"Dell XPS 15\",\"quantity\":1,\"unit_price\":1499,\"amount\":1499}]', 'completed', '2025-06-04 06:52:25'),
(66, 'INVO-20250604-25712', 'ORD-20250604-21554', '2025-06-04', 'sanka dineth', '0717824781', 999.00, 'Bank Transfer', '[{\"product_name\":\"Alienware X51 R3\",\"quantity\":1,\"unit_price\":\"999.00\",\"amount\":\"999.00\"}]', 'completed', '2025-06-04 13:18:31'),
(67, 'INVO-20250604-33621', 'ORD-20250604-44404', '2025-06-04', 'prabath silva', '0775614287', 11513.91, 'Bank Transfer', '[{\"product_name\":\"Acer Aspire 5\",\"quantity\":3,\"unit_price\":\"499.99\",\"amount\":\"1499.97\"},{\"product_name\":\"Lenovo ThinkBook 14s\",\"quantity\":5,\"unit_price\":\"899.00\",\"amount\":\"4495.00\"},{\"product_name\":\"Alienware Aurora Ryzen Edition\",\"quantity\":1,\"unit_price\":\"1599.00\",\"amount\":\"1599.00\"},{\"product_name\":\"iBUYPOWER Revolt 3\",\"quantity\":2,\"unit_price\":\"1199.99\",\"amount\":\"2399.98\"},{\"product_name\":\"HP 27f 4K Monitor\",\"quantity\":4,\"unit_price\":\"379.99\",\"amount\":\"1519.96\"}]', 'completed', '2025-06-04 18:32:46'),
(68, 'INVO-20250605-56744', 'ORD-20250605-29187', '2025-06-05', 'pasindu lakshan', '0784518764', 1199.99, 'Cash', '[{\"product_name\":\"Samsung Galaxy Book Ion\",\"quantity\":1,\"unit_price\":\"1199.99\",\"amount\":\"1199.99\"}]', 'completed', '2025-06-05 05:31:01'),
(69, 'INVO-20250605-41205', 'ORD-20250605-32579', '2025-06-05', 'kasuni gunasekara', '0714523678', 2097.00, 'Cash', '[{\"product_name\":\"MSI Pro DP21\",\"quantity\":3,\"unit_price\":\"699.00\",\"amount\":\"2097.00\"}]', 'completed', '2025-06-05 09:24:04'),
(70, 'INVO-20250605-64671', 'ORD-20250603-62958', '2025-06-05', 'ruwan', '0714578954', 599.00, 'Cash', '[{\"product_name\":null,\"quantity\":1,\"unit_price\":\"599.00\",\"amount\":\"599.00\"}]', 'completed', '2025-06-05 09:24:37'),
(71, 'INVO-20250607-70420', 'ORD-20250607-51769', '2025-06-07', 'test order', '789', 3198.00, 'Bank Transfer', '[{\"product_name\":\"Alienware Aurora Ryzen Edition\",\"quantity\":2,\"unit_price\":\"1599.00\",\"amount\":\"3198.00\"}]', 'completed', '2025-06-07 16:49:15'),
(72, 'INVO-20250608-17286', 'ORD-20250608-44865', '2025-06-08', 'danm test', '789', 1099.00, 'Cash', '[{\"product_name\":\"Lenovo Legion 5\",\"quantity\":1,\"unit_price\":\"1099.00\",\"amount\":\"1099.00\"}]', 'completed', '2025-06-08 07:35:01'),
(73, 'INVO-20250608-81731', 'ORD-20250608-21741', '2025-06-08', 'test order 01', 'test order 01', 599.00, 'Cash', '[{\"product_name\":\"Acer Veriton N\",\"quantity\":1,\"unit_price\":\"599.00\",\"amount\":\"599.00\"}]', 'completed', '2025-06-08 12:11:26'),
(74, 'INVO-20250608-83501', 'ORD-20250608-23355', '2025-06-08', 'complete in Admin', 'test order 02', 3098.00, 'Cash', '[{\"product_name\":\"Apple iMac 24\\\"\",\"quantity\":1,\"unit_price\":1299,\"amount\":1299},{\"product_name\":\"Apple iMac 27\\\"\",\"quantity\":1,\"unit_price\":1799,\"amount\":1799}]', 'completed', '2025-06-08 18:12:03'),
(75, 'INVO-20250609-63659', 'ORD-20250609-13037', '2025-06-09', 'test order 04', 'test order 04', 2398.99, 'Bank Transfer', '[{\"product_name\":\"HP EliteDesk 800 G6\",\"quantity\":1,\"unit_price\":\"899.99\",\"amount\":\"899.99\"},{\"product_name\":\"Alienware Aurora R12\",\"quantity\":1,\"unit_price\":\"1499.00\",\"amount\":\"1499.00\"}]', 'completed', '2025-06-09 07:20:37'),
(76, 'INVO-20250609-62767', 'ORD-20250609-56370', '2025-06-09', 'test order 05 user', 'test order 05 user', 3198.00, 'Cash', '[{\"product_name\":\"Alienware Aurora Ryzen Edition\",\"quantity\":2,\"unit_price\":\"1599.00\",\"amount\":\"3198.00\"}]', 'completed', '2025-06-09 10:04:17'),
(77, 'INVO-20250609-49947', 'ORD-20250608-57810', '2025-06-09', 'test order 03', 'test order 03', 999.99, 'Card', '[{\"product_name\":\"iBUYPOWER Slate MR\",\"quantity\":1,\"unit_price\":\"999.99\",\"amount\":\"999.99\"}]', 'completed', '2025-06-09 10:10:07'),
(78, 'INVO-20250609-57105', 'ORD-20250609-22878', '2025-06-09', 'test order 06 user', 'test order 06 user', 1199.00, 'Bank Transfer', '[{\"product_name\":\"Lenovo Legion T5\",\"quantity\":1,\"unit_price\":\"1199.00\",\"amount\":\"1199.00\"}]', 'completed', '2025-06-09 10:41:46'),
(79, 'INVO-20250612-43394', 'ORD-20250612-57023', '2025-06-12', 'test order 07 user', 'test order 07 user', 3096.98, 'Cash', '[{\"product_name\":\"Acer Aspire C27\",\"quantity\":2,\"unit_price\":\"799.99\",\"amount\":\"1599.98\"},{\"product_name\":\"Acer Chromebox CXI4\",\"quantity\":3,\"unit_price\":\"499.00\",\"amount\":\"1497.00\"}]', 'completed', '2025-06-12 07:20:40'),
(80, 'INVO-20250628-37378', 'ORD-20250628-56308', '2025-06-28', 'testorder08', 'testorder08', 3198.98, 'Bank Transfer', '[{\"product_name\":\"Lenovo ThinkCentre M90a\",\"quantity\":1,\"unit_price\":\"999.00\",\"amount\":\"999.00\"},{\"product_name\":\"iBUYPOWER Slate MR\",\"quantity\":1,\"unit_price\":\"999.99\",\"amount\":\"999.99\"},{\"product_name\":\"iBUYPOWER Revolt 3\",\"quantity\":1,\"unit_price\":\"1199.99\",\"amount\":\"1199.99\"}]', 'cancelled', '2025-06-28 11:16:18'),
(81, 'INVO-20250701-40242', 'ORD-20250701-52419', '2025-07-01', 'Rusiri Thilskshi', '0766736110', 13798.97, 'Card', '[{\"product_name\":\"Samsung Galaxy Book S\",\"quantity\":1,\"unit_price\":\"999.99\",\"amount\":\"999.99\"},{\"product_name\":\"MSI Aegis SE\",\"quantity\":1,\"unit_price\":\"999.99\",\"amount\":\"999.99\"},{\"product_name\":\"Acer Aspire C27\",\"quantity\":1,\"unit_price\":\"799.99\",\"amount\":\"799.99\"},{\"product_name\":\"Alienware X51 R3\",\"quantity\":1,\"unit_price\":\"999.00\",\"amount\":\"999.00\"},{\"product_name\":\"Battery\",\"quantity\":2,\"unit_price\":\"5000.00\",\"amount\":\"10000.00\"}]', 'completed', '2025-07-01 11:30:35'),
(82, 'INVO-20250701-48397', 'ORD-20250628-56308', '2025-07-01', 'testorder08', 'testorder08', 3599.97, 'Bank Transfer', '[{\"product_name\":\"HP Z2 G8 Workstation\",\"quantity\":1,\"unit_price\":1299.990000000000009094947017729282379150390625,\"amount\":1299.990000000000009094947017729282379150390625},{\"product_name\":\"HP Omen 15\",\"quantity\":1,\"unit_price\":1299.990000000000009094947017729282379150390625,\"amount\":1299.990000000000009094947017729282379150390625},{\"product_name\":\"iBUYPOWER Slate MR\",\"quantity\":1,\"unit_price\":999.990000000000009094947017729282379150390625,\"amount\":999.990000000000009094947017729282379150390625}]', 'completed', '2025-07-01 12:18:42'),
(83, 'INVO-20250923-92045', 'ORD-20250922-10008', '2025-09-23', 'test_order', '075945789', 499.99, 'Card', '[{\"product_name\":\"Acer Aspire 5\",\"quantity\":1,\"unit_price\":\"499.99\",\"amount\":\"499.99\"}]', 'completed', '2025-09-23 13:39:57'),
(84, 'INVO-20250924-91252', 'ORD-20250924-39043', '2025-09-24', 'test_order_02', '0456785236', 1599.00, 'Cash', '[{\"product_name\":\"Alienware Aurora Ryzen Edition\",\"quantity\":1,\"unit_price\":\"1599.00\",\"amount\":\"1599.00\"}]', 'completed', '2025-09-24 04:32:12'),
(85, 'INVO-20250924-48154', 'ORD-20250924-69224', '2025-09-24', 'ashan sanju', '0764589123', 549.99, 'Cash', '[{\"product_name\":\"Dell Inspiron 15\",\"quantity\":1,\"unit_price\":\"549.99\",\"amount\":\"549.99\"}]', 'cancelled', '2025-09-24 11:18:24'),
(86, 'INVO-20250925-79620', 'ORD-20250924-69224', '2025-09-25', 'ashan sanju', '0764589123', 2348.99, 'Cash', '[{\"product_name\":\"Dell Inspiron 15\",\"quantity\":1,\"unit_price\":549.99,\"amount\":549.99},{\"product_name\":\"Apple iMac 27\\\"\",\"quantity\":1,\"unit_price\":1799,\"amount\":1799}]', 'completed', '2025-09-25 13:53:50'),
(87, 'INVO-20250927-85999', 'ORD-20250927-30931', '2025-09-27', 'test_order_03', '0741245789', 549.99, 'Cash', '[{\"product_name\":\"Dell Inspiron 3880\",\"quantity\":1,\"unit_price\":\"549.99\",\"amount\":\"549.99\"}]', 'completed', '2025-09-27 14:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` text,
  `contact_number` varchar(20) DEFAULT NULL,
  `order_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','canceled','modified') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_name`, `customer_address`, `contact_number`, `order_date`, `total_amount`, `payment_method`, `status`, `created_at`) VALUES
(88, 'ORD-20250922-10008', 'test_order', '123,horana', '075945789', '2025-09-22', 499.99, 'Card', 'completed', '2025-09-22 10:24:57'),
(66, 'ORD-20250603-98798', 'test4', 'test4', 'test4', '2025-06-03', 749.99, 'Bank Transfer', 'completed', '2025-06-03 12:02:29'),
(68, 'ORD-20250603-99710', 'kamal silva', '123, hoarana', '0741578549', '2025-06-03', 699.99, 'Bank Transfer', 'completed', '2025-06-03 12:49:02'),
(67, 'ORD-20250603-69043', 'test5', 'test5', 'test5', '2025-06-03', 7499.90, 'Bank Transfer', 'completed', '2025-06-03 12:06:45'),
(65, 'ORD-20250603-74634', 'qwerty', '123, pugoda', '072 1245789', '2025-06-03', 849.99, 'Card', 'completed', '2025-06-03 11:58:29'),
(64, 'ORD-20250603-62958', 'ruwan', '123, gampaha', '0714578954', '2025-06-03', 599.00, 'Cash', 'completed', '2025-06-03 11:19:52'),
(63, 'ORD-20250603-71590', 'test3', 'test3', 'test3', '2025-06-03', 899.00, 'Bank Transfer', 'completed', '2025-06-03 11:18:46'),
(60, 'ORD-20250603-47997', 'danm', 'kaluthara', '076', '2025-06-03', 999.00, 'Bank Transfer', 'completed', '2025-06-03 10:13:15'),
(61, 'ORD-20250603-54326', 'test2', 'test2', 'test2', '2025-06-03', 499.99, 'Bank Transfer', 'completed', '2025-06-03 10:21:47'),
(70, 'ORD-20250603-48225', 'janeesha weerasigha', '143/B, kaluthara road, horana', '07658422175', '2025-06-03', 1599.98, 'Card', 'completed', '2025-06-03 16:59:03'),
(71, 'ORD-20250603-94094', 'thilina dishan', '457/C, Katukurunda', '0704789512', '2025-06-03', 7995.00, 'Card', 'completed', '2025-06-03 17:19:38'),
(72, 'ORD-20250604-13591', 'ashan sanjula', '123, retiyala, govinna', '0721547893', '2025-06-04', 4097.98, 'Bank Transfer', 'completed', '2025-06-04 06:51:06'),
(73, 'ORD-20250604-21554', 'sanka dineth', '123, kaluthara', '0717824781', '2025-06-04', 999.00, 'Bank Transfer', 'completed', '2025-06-04 13:18:23'),
(74, 'ORD-20250604-44404', 'prabath silva', '782/B, kotahena, migammuwa', '0775614287', '2025-06-04', 11513.91, 'Bank Transfer', 'completed', '2025-06-04 18:32:34'),
(75, 'ORD-20250605-29187', 'pasindu lakshan', '456,beruwala', '0784518764', '2025-06-05', 1199.99, 'Cash', 'completed', '2025-06-05 05:30:50'),
(76, 'ORD-20250605-32579', 'kasuni gunasekara', '123, padukka', '0714523678', '2025-06-05', 2097.00, 'Cash', 'completed', '2025-06-05 09:23:51'),
(77, 'ORD-20250607-51769', 'test order', '456, kahathuduwa', '789', '2025-06-07', 3198.00, 'Bank Transfer', 'completed', '2025-06-07 16:49:02'),
(78, 'ORD-20250608-44865', 'danm test', '44, kalawana', '789', '2025-06-08', 1099.00, 'Cash', 'completed', '2025-06-08 07:34:53'),
(79, 'ORD-20250608-21741', 'test order 01', 'test order 01', 'test order 01', '2025-06-08', 599.00, 'Cash', 'completed', '2025-06-08 12:11:02'),
(80, 'ORD-20250608-23355', 'complete in Admin', 'test order 02', 'test order 02', '2025-06-08', 3098.00, 'Cash', 'modified', '2025-06-08 12:15:33'),
(81, 'ORD-20250608-57810', 'test order 03', 'test order 03', 'test order 03', '2025-06-08', 999.99, 'Card', 'completed', '2025-06-08 18:03:12'),
(82, 'ORD-20250609-13037', 'test order 04', 'test order 04', 'test order 04', '2025-06-09', 2398.99, 'Bank Transfer', 'completed', '2025-06-09 07:20:24'),
(83, 'ORD-20250609-56370', 'test order 05 user', 'test order 05 user', 'test order 05 user', '2025-06-09', 3198.00, 'Cash', 'completed', '2025-06-09 10:02:41'),
(84, 'ORD-20250609-22878', 'test order 06 user', 'test order 06 user', 'test order 06 user', '2025-06-09', 1199.00, 'Bank Transfer', 'completed', '2025-06-09 10:41:32'),
(85, 'ORD-20250612-57023', 'test order 07 user', 'test order 07 user', 'test order 07 user', '2025-06-12', 3096.98, 'Cash', 'completed', '2025-06-12 07:20:28'),
(86, 'ORD-20250628-56308', 'testorder08', 'testorder08', 'testorder08', '2025-06-28', 3599.97, 'Bank Transfer', 'modified', '2025-06-28 11:16:10'),
(87, 'ORD-20250701-52419', 'Rusiri Thilskshi', 'Halpita', '0766736110', '2025-07-01', 13798.97, 'Card', 'completed', '2025-07-01 11:29:51'),
(89, 'ORD-20250924-39043', 'test_order_02', '123,horana', '0456785236', '2025-09-24', 1599.00, 'Cash', 'completed', '2025-09-24 04:32:06'),
(91, 'ORD-20250924-69224', 'ashan sanju', '123,Colombo', '0764589123', '2025-09-24', 2348.99, 'Cash', 'modified', '2025-09-24 08:42:18'),
(92, 'ORD-20250927-30931', 'test_order_03', '123,horana', '0741245789', '2025-09-27', 549.99, 'Cash', 'completed', '2025-09-27 14:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_category` varchar(255) DEFAULT NULL,
  `product_description` text,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `fk_order_items_orders` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=217 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `amount`, `product_name`, `product_category`, `product_description`) VALUES
(157, 'ORD-20250603-74634', 659, 1, 849.99, 849.99, 'Acer Nitro 5', 'Laptop', NULL),
(158, 'ORD-20250603-98798', 629, 1, 749.99, 749.99, 'Acer Swift 5', 'Laptop', NULL),
(152, 'ORD-20250603-71590', 666, 1, 899.00, 899.00, NULL, NULL, NULL),
(154, 'ORD-20250603-62958', 719, 1, 599.00, 599.00, NULL, NULL, NULL),
(166, 'ORD-20250603-54991', 649, 1, 629.99, 629.99, 'Acer Chromebook Spin 713', 'Laptop', NULL),
(160, 'ORD-20250603-69043', 629, 10, 749.99, 7499.90, 'Acer Swift 5', 'Laptop', NULL),
(147, 'ORD-20250603-47997', 713, 1, 999.00, 999.00, NULL, NULL, NULL),
(148, 'ORD-20250603-54326', 639, 1, 499.99, 499.99, 'Acer Aspire 5', 'Laptop', NULL),
(165, 'ORD-20250603-99710', 729, 1, 699.99, 699.99, 'Asus ROG Swift PG279Q Monitor', 'Peripheral Device', NULL),
(167, 'ORD-20250603-48225', 623, 1, 999.99, 999.99, 'Dell XPS 13', 'Laptop', NULL),
(168, 'ORD-20250603-48225', 686, 1, 599.99, 599.99, 'HP Pavilion TP01', 'Desktop', NULL),
(169, 'ORD-20250603-94094', 693, 5, 1599.00, 7995.00, 'Alienware Aurora R13', 'Desktop', NULL),
(174, 'ORD-20250604-13591', 675, 1, 1299.00, 1299.00, 'Apple iMac 24\"', 'Desktop', NULL),
(175, 'ORD-20250604-13591', 710, 2, 649.99, 1299.98, 'MSI Cubi 5', 'Desktop', NULL),
(176, 'ORD-20250604-13591', 673, 1, 1499.00, 1499.00, 'Dell XPS 15', 'Laptop', NULL),
(177, 'ORD-20250604-21554', 713, 1, 999.00, 999.00, 'Alienware X51 R3', 'Desktop', NULL),
(178, 'ORD-20250604-44404', 703, 1, 1599.00, 1599.00, 'Alienware Aurora Ryzen Edition', 'Desktop', NULL),
(179, 'ORD-20250604-44404', 712, 2, 1199.99, 2399.98, 'iBUYPOWER Revolt 3', 'Desktop', NULL),
(180, 'ORD-20250604-44404', 639, 3, 499.99, 1499.97, 'Acer Aspire 5', 'Laptop', NULL),
(181, 'ORD-20250604-44404', 727, 4, 379.99, 1519.96, 'HP 27f 4K Monitor', 'Peripheral Device', NULL),
(182, 'ORD-20250604-44404', 666, 5, 899.00, 4495.00, 'Lenovo ThinkBook 14s', 'Laptop', NULL),
(183, 'ORD-20250605-29187', 662, 1, 1199.99, 1199.99, 'Samsung Galaxy Book Ion', 'Laptop', NULL),
(184, 'ORD-20250605-32579', 700, 3, 699.00, 2097.00, 'MSI Pro DP21', 'Desktop', NULL),
(185, 'ORD-20250607-51769', 703, 2, 1599.00, 3198.00, 'Alienware Aurora Ryzen Edition', 'Desktop', NULL),
(186, 'ORD-20250608-44865', 656, 1, 1099.00, 1099.00, 'Lenovo Legion 5', 'Laptop', NULL),
(187, 'ORD-20250608-21741', 719, 1, 599.00, 599.00, 'Acer Veriton N', 'Desktop', NULL),
(192, 'ORD-20250608-23355', 695, 1, 1799.00, 1799.00, 'Apple iMac 27\"', 'Desktop', NULL),
(191, 'ORD-20250608-23355', 675, 1, 1299.00, 1299.00, 'Apple iMac 24\"', 'Desktop', NULL),
(190, 'ORD-20250608-57810', 702, 1, 999.99, 999.99, 'iBUYPOWER Slate MR', 'Desktop', NULL),
(193, 'ORD-20250609-13037', 683, 1, 1499.00, 1499.00, 'Alienware Aurora R12', 'Desktop', NULL),
(194, 'ORD-20250609-13037', 676, 1, 899.99, 899.99, 'HP EliteDesk 800 G6', 'Desktop', NULL),
(195, 'ORD-20250609-56370', 703, 2, 1599.00, 3198.00, 'Alienware Aurora Ryzen Edition', 'Desktop', NULL),
(196, 'ORD-20250609-22878', 697, 1, 1199.00, 1199.00, 'Lenovo Legion T5', 'Desktop', NULL),
(197, 'ORD-20250612-57023', 709, 3, 499.00, 1497.00, 'Acer Chromebox CXI4', 'Desktop', NULL),
(198, 'ORD-20250612-57023', 699, 2, 799.99, 1599.98, 'Acer Aspire C27', 'Desktop', NULL),
(209, 'ORD-20250628-56308', 702, 1, 999.99, 999.99, 'iBUYPOWER Slate MR', 'Desktop', NULL),
(208, 'ORD-20250628-56308', 665, 1, 1299.99, 1299.99, 'HP Omen 15', 'Laptop', NULL),
(207, 'ORD-20250628-56308', 706, 1, 1299.99, 1299.99, 'HP Z2 G8 Workstation', 'Desktop', NULL),
(202, 'ORD-20250701-52419', 733, 2, 5000.00, 10000.00, 'Battery', 'Peripheral Device', NULL),
(203, 'ORD-20250701-52419', 699, 1, 799.99, 799.99, 'Acer Aspire C27', 'Desktop', NULL),
(204, 'ORD-20250701-52419', 713, 1, 999.00, 999.00, 'Alienware X51 R3', 'Desktop', NULL),
(205, 'ORD-20250701-52419', 672, 1, 999.99, 999.99, 'Samsung Galaxy Book S', 'Laptop', NULL),
(206, 'ORD-20250701-52419', 690, 1, 999.99, 999.99, 'MSI Aegis SE', 'Desktop', NULL),
(210, 'ORD-20250922-10008', 639, 1, 499.99, 499.99, 'Acer Aspire 5', 'Laptop', NULL),
(211, 'ORD-20250924-39043', 703, 1, 1599.00, 1599.00, 'Alienware Aurora Ryzen Edition', 'Desktop', NULL),
(215, 'ORD-20250924-69224', 695, 1, 1799.00, 1799.00, 'Apple iMac 27\"', 'Desktop', NULL),
(214, 'ORD-20250924-69224', 633, 1, 549.99, 549.99, 'Dell Inspiron 15', 'Laptop', NULL),
(216, 'ORD-20250927-30931', 684, 1, 549.99, 549.99, 'Dell Inspiron 3880', 'Desktop', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

DROP TABLE IF EXISTS `proposals`;
CREATE TABLE IF NOT EXISTS `proposals` (
  `proposal_id` varchar(20) NOT NULL,
  `date_created` date NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_address` text,
  `contact_number` varchar(20) DEFAULT NULL,
  `validity_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `terms` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` tinyint(1) DEFAULT '0',
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`proposal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`proposal_id`, `date_created`, `customer_name`, `customer_address`, `contact_number`, `validity_date`, `total_amount`, `terms`, `created_at`, `modified`, `last_modified`) VALUES
('PROP-20250608-93153', '2025-06-08', 'test proposal 02', 'test proposal 02', 'test proposal 02', '2025-06-10', 799.99, 'test proposal 02', '2025-06-08 12:14:28', 0, NULL),
('PROP-20250608-96438', '2025-06-08', 'test proposal 01', 'test proposal 01', 'test proposal 01', '2025-06-09', 599.99, 'test proposal 01', '2025-06-08 12:12:21', 0, NULL),
('PROP-20250608-47829', '2025-06-08', 'test proposal 01', 'test proposal 01', 'test proposal 01', '2025-06-09', 599.99, 'test proposal 01', '2025-06-08 12:12:21', 0, NULL),
('PROP-20250608-85033', '2025-06-08', 'test proposal 02', 'test proposal 02', 'test proposal 02', '2025-06-10', 799.99, 'test proposal 02', '2025-06-08 12:14:28', 0, NULL),
('PROP-20250603-83548', '2025-06-03', 'sameera disasnayaka', '123, mathugama', '0784578125', '2025-06-24', 1299.00, 'Terms & Condition This project estimate is an approximation based on information and requirements provided by the client and is not guaranteed. Actual costs and terms may change once all project elements are discussed, negotiated, and finalized. Prior to any change in costs, the client will be notified. This estimate is valid for 7 days.', '2025-06-03 12:16:39', 0, NULL),
('PROP-20250603-44832', '2025-06-03', 'sameera disasnayaka', '123, mathugama', '0784578125', '2025-06-24', 1999.99, 'Terms & Condition This project estimate is an approximation based on information and requirements provided by the client and is not guaranteed. Actual costs and terms may change once all project elements are discussed, negotiated, and finalized. Prior to any change in costs, the client will be notified. This estimate is valid for 7 days.', '2025-06-03 12:16:02', 0, NULL),
('PROP-20250603-39047', '2025-06-03', 'sameera disasnayaka', '123, mathugama', '0784578125', '2025-06-24', 899.99, 'Terms & Condition This project estimate is an approximation based on information and requirements provided by the client and is not guaranteed. Actual costs and terms may change once all project elements are discussed, negotiated, and finalized. Prior to any change in costs, the client will be notified. This estimate is valid for 7 days.', '2025-06-03 10:38:51', 0, NULL),
('PROP-20250603-82736', '2025-06-03', 'ravindu lakshan', '123, bandaragama', '0751248796', '2025-07-01', 2748.98, 'Terms & Condition This project estimate is an approximation based on information and requirements provided by the client and is not guaranteed. Actual costs and terms may change once all project elements are discussed, negotiated, and finalized. Prior to any change in costs, the client will be notified. This estimate is valid for 7 days.', '2025-06-03 10:33:40', 0, NULL),
('PROP-20250609-84816', '2025-06-09', 'test proposal 03', 'test proposal 03', 'test proposal 03', '2025-06-10', 7398.00, 'test proposal 03', '2025-06-09 10:33:00', 0, NULL),
('PROP-20250609-65512', '2025-06-09', 'test proposal 03', 'test proposal 03', 'test proposal 03', '2025-06-10', 7398.00, 'test proposal 03', '2025-06-09 10:33:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `proposal_items`
--

DROP TABLE IF EXISTS `proposal_items`;
CREATE TABLE IF NOT EXISTS `proposal_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proposal_id` varchar(20) DEFAULT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proposal_id` (`proposal_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `proposal_items`
--

INSERT INTO `proposal_items` (`id`, `proposal_id`, `product_id`, `quantity`, `unit_price`, `amount`) VALUES
(53, 'PROP-20250609-84816', 626, 1, 1399.00, 1399.00),
(52, 'PROP-20250609-65512', 715, 1, 5999.00, 5999.00),
(51, 'PROP-20250609-65512', 626, 1, 1399.00, 1399.00),
(50, 'PROP-20250608-93153', 699, 1, 799.99, 799.99),
(49, 'PROP-20250608-85033', 699, 1, 799.99, 799.99),
(48, 'PROP-20250608-47829', 686, 1, 599.99, 599.99),
(47, 'PROP-20250608-96438', 686, 1, 599.99, 599.99),
(46, 'PROP-20250603-83548', 671, 1, 1299.00, 1299.00),
(45, 'PROP-20250603-44832', 630, 1, 1999.99, 1999.99),
(44, 'PROP-20250603-39047', 627, 1, 899.99, 899.99),
(43, 'PROP-20250603-82736', 712, 1, 1199.99, 1199.99),
(42, 'PROP-20250603-82736', 635, 1, 849.99, 849.99),
(41, 'PROP-20250603-82736', 685, 1, 699.00, 699.00),
(54, 'PROP-20250609-84816', 715, 1, 5999.00, 5999.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) DEFAULT NULL,
  `invoice_id` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `invoice_id`, `customer_name`, `contact_number`, `sale_date`, `total_amount`, `payment_method`, `status`, `created_at`) VALUES
(64, 'ORD-20250603-69043', 'INVO-20250603-97927', 'test5', 'test5', '2025-06-03', 3749.95, 'Bank Transfer', 'cancelled', '2025-06-03 12:07:07'),
(61, 'ORD-20250603-74634', 'INVO-20250603-50815', 'qwerty', '072 1245789', '2025-06-03', 699.00, 'Bank Transfer', 'cancelled', '2025-06-03 11:59:37'),
(62, 'ORD-20250603-74634', 'INVO-20250603-67178', 'qwerty', '072 1245789', '2025-06-03', 849.99, 'Card', 'cancelled', '2025-06-03 12:00:14'),
(63, 'ORD-20250603-98798', 'INVO-20250603-12771', 'test4', 'test4', '2025-06-03', 749.99, 'Bank Transfer', 'cancelled', '2025-06-03 12:02:37'),
(59, 'ORD-20250603-54326', 'INVO-20250603-43061', 'test2', 'test2', '2025-06-03', 499.99, 'Bank Transfer', 'completed', '2025-06-03 10:21:54'),
(58, 'ORD-20250603-47997', 'INVO-20250603-82536', 'danm', '076', '2025-06-03', 999.00, 'Bank Transfer', 'cancelled', '2025-06-03 10:13:47'),
(57, 'ORD-20250603-47997', 'INVO-20250603-77745', 'test1', 'test1', '2025-06-03', 799.99, 'Cash', 'cancelled', '2025-06-03 10:13:20'),
(65, 'ORD-20250603-69043', 'INVO-20250603-74290', 'test5', 'test5', '2025-06-03', 7499.90, 'Bank Transfer', 'cancelled', '2025-06-03 12:07:39'),
(66, 'ORD-20250603-99710', 'INVO-20250603-31015', 'kamal silva', '0741578549', '2025-06-03', 2498.99, 'Bank Transfer', 'cancelled', '2025-06-03 12:49:58'),
(67, 'ORD-20250603-99710', 'INVO-20250603-83381', 'kamal silva', '0741578549', '2025-06-03', 699.99, 'Bank Transfer', 'completed', '2025-06-03 12:50:15'),
(68, 'ORD-20250603-54991', 'INVO-20250603-31939', 'danm withanage', '0766401959', '2025-06-03', 629.99, 'Card', 'completed', '2025-06-03 13:47:36'),
(69, 'ORD-20250603-48225', 'INVO-20250603-57996', 'janeesha weerasigha', '07658422175', '2025-06-03', 1599.98, 'Card', 'completed', '2025-06-03 16:59:23'),
(70, 'ORD-20250603-94094', 'INVO-20250603-97982', 'thilina dishan', '0704789512', '2025-06-03', 7995.00, 'Card', 'completed', '2025-06-03 17:19:50'),
(71, 'ORD-20250604-13591', 'INVO-20250604-25913', 'ashan sanjula', '0721547893', '2025-06-04', 1798.98, 'Bank Transfer', 'cancelled', '2025-06-04 06:51:48'),
(72, 'ORD-20250604-13591', 'INVO-20250604-49818', 'ashan sanjula', '0721547893', '2025-06-04', 4097.98, 'Bank Transfer', 'completed', '2025-06-04 06:52:25'),
(73, 'ORD-20250604-21554', 'INVO-20250604-25712', 'sanka dineth', '0717824781', '2025-06-04', 999.00, 'Bank Transfer', 'completed', '2025-06-04 13:18:31'),
(74, 'ORD-20250604-44404', 'INVO-20250604-33621', 'prabath silva', '0775614287', '2025-06-04', 11513.91, 'Bank Transfer', 'completed', '2025-06-04 18:32:46'),
(75, 'ORD-20250605-29187', 'INVO-20250605-56744', 'pasindu lakshan', '0784518764', '2025-06-05', 1199.99, 'Cash', 'completed', '2025-06-05 05:31:01'),
(76, 'ORD-20250605-32579', 'INVO-20250605-41205', 'kasuni gunasekara', '0714523678', '2025-06-05', 2097.00, 'Cash', 'completed', '2025-06-05 09:24:04'),
(77, 'ORD-20250603-62958', 'INVO-20250605-64671', 'ruwan', '0714578954', '2025-06-05', 599.00, 'Cash', 'completed', '2025-06-05 09:24:37'),
(78, 'ORD-20250607-51769', 'INVO-20250607-70420', 'test order', '789', '2025-06-07', 3198.00, 'Bank Transfer', 'completed', '2025-06-07 16:49:15'),
(79, 'ORD-20250608-44865', 'INVO-20250608-17286', 'danm test', '789', '2025-06-08', 1099.00, 'Cash', 'completed', '2025-06-08 07:35:01'),
(80, 'ORD-20250608-21741', 'INVO-20250608-81731', 'test order 01', 'test order 01', '2025-06-08', 599.00, 'Cash', 'completed', '2025-06-08 12:11:26'),
(81, 'ORD-20250608-23355', 'INVO-20250608-83501', 'complete in Admin', 'test order 02', '2025-06-08', 3098.00, 'Cash', 'completed', '2025-06-08 18:12:03'),
(82, 'ORD-20250609-13037', 'INVO-20250609-63659', 'test order 04', 'test order 04', '2025-06-09', 2398.99, 'Bank Transfer', 'completed', '2025-06-09 07:20:37'),
(83, 'ORD-20250609-56370', 'INVO-20250609-62767', 'test order 05 user', 'test order 05 user', '2025-06-09', 3198.00, 'Cash', 'completed', '2025-06-09 10:04:17'),
(84, 'ORD-20250608-57810', 'INVO-20250609-49947', 'test order 03', 'test order 03', '2025-06-09', 999.99, 'Card', 'completed', '2025-06-09 10:10:07'),
(85, 'ORD-20250609-22878', 'INVO-20250609-57105', 'test order 06 user', 'test order 06 user', '2025-06-09', 1199.00, 'Bank Transfer', 'completed', '2025-06-09 10:41:46'),
(86, 'ORD-20250612-57023', 'INVO-20250612-43394', 'test order 07 user', 'test order 07 user', '2025-06-12', 3096.98, 'Cash', 'completed', '2025-06-12 07:20:40'),
(87, 'ORD-20250628-56308', 'INVO-20250628-37378', 'testorder08', 'testorder08', '2025-06-28', 3198.98, 'Bank Transfer', 'cancelled', '2025-06-28 11:16:18'),
(88, 'ORD-20250701-52419', 'INVO-20250701-40242', 'Rusiri Thilskshi', '0766736110', '2025-07-01', 13798.97, 'Card', 'completed', '2025-07-01 11:30:35'),
(89, 'ORD-20250628-56308', 'INVO-20250701-48397', 'testorder08', 'testorder08', '2025-07-01', 3599.97, 'Bank Transfer', 'completed', '2025-07-01 12:18:42'),
(90, 'ORD-20250922-10008', 'INVO-20250923-92045', 'test_order', '075945789', '2025-09-23', 499.99, 'Card', 'completed', '2025-09-23 13:39:57'),
(91, 'ORD-20250924-39043', 'INVO-20250924-91252', 'test_order_02', '0456785236', '2025-09-24', 1599.00, 'Cash', 'completed', '2025-09-24 04:32:12'),
(92, 'ORD-20250924-69224', 'INVO-20250924-48154', 'ashan sanju', '0764589123', '2025-09-24', 549.99, 'Cash', 'cancelled', '2025-09-24 11:18:24'),
(93, 'ORD-20250924-69224', 'INVO-20250925-79620', 'ashan sanju', '0764589123', '2025-09-25', 2348.99, 'Cash', 'completed', '2025-09-25 13:53:50'),
(94, 'ORD-20250927-30931', 'INVO-20250927-85999', 'test_order_03', '0741245789', '2025-09-27', 549.99, 'Cash', 'completed', '2025-09-27 14:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `sales_products`
--

DROP TABLE IF EXISTS `sales_products`;
CREATE TABLE IF NOT EXISTS `sales_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sale_id` int DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_products`
--

INSERT INTO `sales_products` (`id`, `sale_id`, `product_name`, `quantity`, `unit_price`, `total`) VALUES
(92, 79, 'Lenovo Legion 5', 1, 1099.00, 1099.00),
(91, 78, 'Alienware Aurora Ryzen Edition', 2, 1599.00, 3198.00),
(90, 77, NULL, 1, 599.00, 599.00),
(89, 76, 'MSI Pro DP21', 3, 699.00, 2097.00),
(88, 75, 'Samsung Galaxy Book Ion', 1, 1199.99, 1199.99),
(87, 74, 'HP 27f 4K Monitor', 4, 379.99, 1519.96),
(86, 74, 'iBUYPOWER Revolt 3', 2, 1199.99, 2399.98),
(85, 74, 'Alienware Aurora Ryzen Edition', 1, 1599.00, 1599.00),
(84, 74, 'Lenovo ThinkBook 14s', 5, 899.00, 4495.00),
(83, 74, 'Acer Aspire 5', 3, 499.99, 1499.97),
(82, 73, 'Alienware X51 R3', 1, 999.00, 999.00),
(81, 72, 'Dell XPS 15', 1, 1499.00, 1499.00),
(80, 72, 'MSI Cubi 5', 2, 649.99, 1299.98),
(79, 72, 'Apple iMac 24\"', 1, 1299.00, 1299.00),
(78, 71, 'MSI Cubi 5', 2, 649.99, 1299.98),
(77, 71, 'Acer Chromebox CXI4', 1, 499.00, 499.00),
(76, 70, 'Alienware Aurora R13', 5, 1599.00, 7995.00),
(75, 69, 'HP Pavilion TP01', 1, 599.99, 599.99),
(74, 69, 'Dell XPS 13', 1, 999.99, 999.99),
(73, 68, 'Acer Chromebook Spin 713', 1, 629.99, 629.99),
(72, 67, 'Asus ROG Swift PG279Q Monitor', 1, 699.99, 699.99),
(71, 66, 'Asus ROG Swift PG279Q Monitor', 1, 699.99, 699.99),
(63, 59, 'Acer Aspire 5', 1, 499.99, 499.99),
(70, 66, 'Apple iMac 27\"', 1, 1799.00, 1799.00),
(93, 80, 'Acer Veriton N', 1, 599.00, 599.00),
(94, 81, 'Apple iMac 24\"', 1, 1299.00, 1299.00),
(95, 81, 'Apple iMac 27\"', 1, 1799.00, 1799.00),
(96, 82, 'HP EliteDesk 800 G6', 1, 899.99, 899.99),
(97, 82, 'Alienware Aurora R12', 1, 1499.00, 1499.00),
(98, 83, 'Alienware Aurora Ryzen Edition', 2, 1599.00, 3198.00),
(99, 84, 'iBUYPOWER Slate MR', 1, 999.99, 999.99),
(100, 85, 'Lenovo Legion T5', 1, 1199.00, 1199.00),
(101, 86, 'Acer Aspire C27', 2, 799.99, 1599.98),
(102, 86, 'Acer Chromebox CXI4', 3, 499.00, 1497.00),
(103, 87, 'Lenovo ThinkCentre M90a', 1, 999.00, 999.00),
(104, 87, 'iBUYPOWER Slate MR', 1, 999.99, 999.99),
(105, 87, 'iBUYPOWER Revolt 3', 1, 1199.99, 1199.99),
(106, 88, 'Samsung Galaxy Book S', 1, 999.99, 999.99),
(107, 88, 'MSI Aegis SE', 1, 999.99, 999.99),
(108, 88, 'Acer Aspire C27', 1, 799.99, 799.99),
(109, 88, 'Alienware X51 R3', 1, 999.00, 999.00),
(110, 88, 'Battery', 2, 5000.00, 10000.00),
(111, 89, 'HP Z2 G8 Workstation', 1, 1299.99, 1299.99),
(112, 89, 'HP Omen 15', 1, 1299.99, 1299.99),
(113, 89, 'iBUYPOWER Slate MR', 1, 999.99, 999.99),
(114, 90, 'Acer Aspire 5', 1, 499.99, 499.99),
(115, 91, 'Alienware Aurora Ryzen Edition', 1, 1599.00, 1599.00),
(116, 92, 'Dell Inspiron 15', 1, 549.99, 549.99),
(117, 93, 'Dell Inspiron 15', 1, 549.99, 549.99),
(118, 93, 'Apple iMac 27\"', 1, 1799.00, 1799.00),
(119, 94, 'Dell Inspiron 3880', 1, 549.99, 549.99);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact`, `email`, `address`, `created_at`) VALUES
(3, 'test supplier', '0766401959', 'test@gmail.com', '123,horana', '2025-05-30 11:45:44'),
(4, 'ABC Company', '011 256 8691', 'qwerty@gmail.com', 'No.25, Colombo 06', '2025-07-01 12:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID`, `first_name`, `last_name`, `email`, `password`, `role`) VALUES
(25, 'danm', 'withanage', 'danm@gmail.com', '$2y$10$XsZwYf8Kx6CuNU2CkHT9r.qkolKbjs./08n1gSvIPiSJnNvv5AVBa', 'admin'),
(26, 'Test', 'User', 'test@gmail.com', '$2y$10$GSXUHZrZq1MxXCtugUOw0uFGctteZ1BTVqQujJ4tss3SJjGJ9ADkK', 'admin'),
(29, 'user', '01', 'user@gmail.com', '$2y$10$QfSY/9AmGO/fA3QBYLKCSObVkDfnnO9rYfFCOzOYExhVw1ueawkcy', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
