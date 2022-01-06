-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2021 at 11:54 AM
-- Server version: 8.0.23-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `meity_training`
--

-- --------------------------------------------------------

--
-- Table structure for table `m_courses`
--

CREATE TABLE `m_courses` (
  `id`int UNSIGNED NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_subject` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `course_status` tinyint NOT NULL DEFAULT '1' COMMENT '1 for active',
  `course_type` tinyint NOT NULL DEFAULT '1' COMMENT '1 for Diploma and 0 for certificate',
  `row_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 for delete',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `m_courses`
--

INSERT INTO `m_courses` (`id`, `course_name`, `course_code`, `course_subject`, `course_status`, `course_type`, `row_delete`, `created_at`, `updated_at`) VALUES
(1, 'Certificate in Semi-Finished Herbal Products Entrepreneurship', 'CSP101', 'Semi-Finished Herbal Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(2, 'Certificate in Boutique, Readymade Wearing Based Startups and Self-Reliance', 'CSP102', 'Boutique, Readymade Wearing Based Startups and Self-Reliance', 1, 0, 0, NULL, NULL),
(3, 'Certificate in Tour and Travels Based Entrepreneurship', 'CSP103', 'Tour and Travels Based Entrepreneurship', 1, 0, 0, NULL, NULL),
(4, 'Certificate in Collection Center Start-Up and Self-Reliance', 'CSP104', 'Collection Center Start-Up and Self-Reliance', 1, 0, 0, NULL, NULL),
(5, 'Certificate in E-SEWA Social Entrepreneurship', 'CSP105', 'E-SEWA Social Entrepreneurship', 1, 0, 0, NULL, NULL),
(6, 'Certificate in Stall Based Skilling and Startup', 'CSP106', 'Stall Based Skilling and Startup', 1, 0, 0, NULL, NULL),
(7, 'Certificate in Worship and Festival Material Startup, Entrepreneurship and Self-Reliance', 'CSP107', 'Worship and Festival Material Startup, Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(8, 'Certificate in Wild Edible Fungi, Crop Farming and Value Added Product Entrepreneurship and Self-Reliance', 'CSP108', 'Wild Edible Fungi, Crop Farming and Value Added Product Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(9, 'Certificate in Mineral Water Plant Entrepreneurship and Self-Reliance', 'CSP109', 'Mineral Water Plant Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(10, 'Certificate in GOBAR Products Entrepreneurship and Self-Reliance', 'CSP110', 'GOBAR Products Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(11, 'Certificate in Medicinal and Aromatic Plants Farming and Value Added Products Entrepreneurship', 'CSP111', 'Medicinal and Aromatic Plants Farming and Value Added Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(12, 'Certificate in Bakery and Confectionery Products Entrepreneurship', 'CSP112', 'Bakery and Confectionery Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(13, 'Certificate in MAHUA Flower Based Food, Beverages and Bakery Products Entrepreneurship', 'CSP113', 'MAHUA Flower Based Food, Beverages and Bakery Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(14, 'Certificate in Beverages Drinking Water and Milk Products Entrepreneurship', 'CSP114', 'Beverages Drinking Water and Milk Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(15, 'Certificate in Ready to Eat Products Entrepreneurship', 'CSP115', 'Ready to Eat Products Entrepreneurship', 1, 0, 0, NULL, NULL),
(16, 'Certificate in Bio fertilizer and other Fertilizer Entrepreneurship', 'CSP116', 'Bio fertilizer and other Fertilizer Entrepreneurship', 1, 0, 0, NULL, NULL),
(17, 'Certificate Course on the Development of Domestic Chemical Products', 'CSP117', 'Development of Domestic Chemical Products', 1, 0, 0, NULL, NULL),
(18, 'Certificate Course in Agro Chemical Products Entrepreneurship and Self-Reliance', 'CSP118', 'Agro Chemical Products Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(19, 'Certificate Course in Medical Chemical Products Entrepreneurship and Self-Reliance', 'CSP119', 'Medical Chemical Products Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(20, 'Certificate Course in Automobile Chemical Products Entrepreneurship and Self-Reliance', 'CSP120', 'Automobile Chemical Products Entrepreneurship and Self-Reliance', 1, 0, 0, NULL, NULL),
(21, 'Certificate in Self Reliant Entrepreneurship in Rural Based MSME', 'CSP121', 'Self-Reliant Entrepreneurship in Rural Based MSME', 1, 0, 0, NULL, NULL),
(22, 'Certificate in Agro Clinical Services Based Entrepreneurship', 'CSP122', 'Agro Clinical Services Based Entrepreneurship', 1, 0, 0, NULL, NULL),
(23, 'Certificate in Farm Machineries Entrepreneurship', 'CSP123', 'Farm Machineries Entrepreneurship', 1, 0, 0, NULL, NULL),
(24, 'Certificate in Dairy Farming and Machinery Entrepreneurship', 'CSP124', 'Dairy Farming and Machinery Entrepreneurship', 1, 0, 0, NULL, NULL),
(25, 'Diploma in Ayurvedic and Ayush Herbal Products Entrepreneurship', 'DSP401', 'Ayurvedic and Ayush Herbal Products Entrepreneurship', 1, 1, 0, NULL, NULL),
(26, 'Diploma in Ayurvedic APIs Based Entrepreneurship', 'DSP402', 'Ayurvedic APIs Based Entrepreneurship', 1, 1, 0, NULL, NULL),
(27, 'Diploma in Renewable and Solar Energy Entrepreneurship', 'DSP403', 'Renewable and Solar Energy Entrepreneurship', 1, 1, 0, NULL, NULL),
(28, 'Diploma in Clean India Skills and Waste to Best Entrepreneurship', 'DSP404', 'Clean India Skills and Waste to Best Entrepreneurship', 1, 1, 0, NULL, NULL),
(29, 'Diploma in Rubber, Plastic and Wood Entrepreneurship', 'DSP405', 'Rubber, Plastic and Wood Entrepreneurship', 1, 1, 0, NULL, NULL),
(30, 'Diploma in Textile Variety MSME and Entrepreneurship', 'DSP406', 'Textile Variety MSME and Entrepreneurship', 1, 1, 0, NULL, NULL),
(31, 'Diploma in Sports items, Wears, Medical Textile and Textile Accessories Entrepreneurship', 'DSP407', 'Sports items, Wears, Medical Textile and Textile Accessories Entrepreneurship', 1, 1, 0, NULL, NULL),
(32, 'Diploma in Semi-Domestic Electronics Goods and Toys Entrepreneurship', 'DSP408', 'Semi-Domestic Electronics Goods and Toys Entrepreneurship', 1, 1, 0, NULL, NULL),
(33, 'Diploma in Semi-Electrical Hardware Goods Entrepreneurship', 'DSP409', 'Semi-Electrical Hardware Goods Entrepreneurship', 1, 1, 0, NULL, NULL),
(34, 'Diploma in Medical Electronics Entrepreneurship and Self-Reliance', 'DSP410', 'Medical Electronics Entrepreneurship and Self-Reliance', 1, 1, 0, NULL, NULL),
(35, 'Diploma in Semi-Conductor and Lighting Goods Entrepreneurship', 'DSP411', 'Semi-Conductor and Lighting Goods Entrepreneurship', 1, 1, 0, NULL, NULL),
(36, 'Diploma in Agro Engineering Entrepreneurship and Self-Reliance', 'DSP412', 'Agro Engineering Entrepreneurship and Self-Reliance', 1, 1, 0, NULL, NULL),
(37, 'Diploma in Food and Food Processing Products Entrepreneurship', 'DSP413', 'Food and Food Processing Products Entrepreneurship', 1, 1, 0, NULL, NULL),
(38, 'Diploma in Cultural Entrepreneurship with 64 Indigenous Art', 'DSP414', 'Cultural Entrepreneurship with 64 Indigenous Art', 1, 1, 0, NULL, NULL),
(39, 'Diploma in Development of Chemical Products Entrepreneurship', 'DSP415', 'Development of Chemical Products Entrepreneurship', 1, 1, 0, NULL, NULL),
(40, 'Diploma in Mining and Road Construction Machinery Services Entrepreneurship and Self-Reliance', 'DSP416', 'Mining and Road Construction Machinery Services Entrepreneurship and Self-Reliance', 1, 1, 0, NULL, NULL),
(41, 'Diploma in Herbal Beauty, Women Accessories and Products Based Entrepreneurship', 'DSP417', 'Herbal Beauty, Women Accessories and Products Based Entrepreneurship', 1, 1, 0, NULL, NULL),
(42, 'Diploma in Bamboo Products Entrepreneurship', 'DSP418', 'Bamboo Products Entrepreneurship', 1, 1, 0, NULL, NULL),
(43, 'Diploma in Organic Farming Entrepreneurship and Self-Reliance', 'DSP419', 'Organic Farming Entrepreneurship and Self-Reliance', 1, 1, 0, NULL, NULL),
(44, 'Diploma in Mining Surveyor, Mining Skills and Entrepreneurship', 'DSP420', 'Mining Surveyor, Mining Skills and Entrepreneurship', 1, 1, 0, NULL, NULL),
(45, 'Diploma in Tribal Entrepreneurship and Self-Reliance', 'DSP421', 'Tribal Entrepreneurship and Self-Reliance', 1, 1, 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `m_courses`
--
ALTER TABLE `m_courses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `m_courses`
--
ALTER TABLE `m_courses`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
