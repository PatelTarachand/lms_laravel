-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 05, 2021 at 01:09 PM
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
-- Table structure for table `t_students`
--

CREATE TABLE `t_students` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_no` bigint NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aadhar_no` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correspondence_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correspondence_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correspondence_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correspondence_address` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correspondence_pin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_no` bigint DEFAULT NULL,
  `twitter_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT NULL COMMENT '1 for active',
  `row_delete` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 for delete',
  `operation` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_students`
--

INSERT INTO `t_students` (`id`, `first_name`, `last_name`, `mother_name`, `father_name`, `dob`, `gender`, `category`, `mobile_no`, `email`, `aadhar_no`, `correspondence_country`, `correspondence_state`, `correspondence_city`, `correspondence_address`, `correspondence_pin`, `whatsapp_no`, `twitter_id`, `facebook_id`, `instagram_id`, `status`, `row_delete`, `operation`, `operation_user`, `operation_date`, `created_at`, `updated_at`) VALUES
(1, 'ram', 'kumar', 'romiyo', 'ramesh', NULL, 'Male', '1', 2020202020, 'r@gmail.com', '2344', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2021-04-05 01:51:50', '2021-04-05 01:51:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_students`
--
ALTER TABLE `t_students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_students`
--
ALTER TABLE `t_students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
