-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2025 at 08:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bremc_family_realtors`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'omri', '$2y$10$26GOmpRehhL6b6rMnMHMK.NQahJfzny3e2GFbQL3MtZVD7TPYdC9m', '2025-03-22 13:49:43', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `other_images` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rooms` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `images` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `title`, `description`, `price`, `location`, `cover_image`, `other_images`, `image`, `video_url`, `created_at`, `rooms`, `type`, `is_featured`, `images`) VALUES
(9, 'House for sale', 'for sale in mapuat', 6000.00, 'kafue', 'img_67e00a51a0f666.55537403.png', '', NULL, NULL, '2025-03-23 13:19:13', 4, 'sale', 0, ''),
(10, 'tired', 'come on man', 4000.00, 'chalala', 'img_67e0106dd120e3.86034944.png', 'img_67e0106dd1c060.69436934.png,img_67e0106dd25f00.50913488.png,img_67e0106dd31ba6.36036523.png,img_67e0106dd55143.35006929.png', NULL, 'https://www.youtube.com/watch?v=yQMes8rjKrs', '2025-03-23 13:45:17', 2, 'rent', 0, ''),
(14, 'come on', 'lets see', 1200.00, 'Zambia', 'img_67e5762cde29d0.06789371.png', 'img_67e5762ce1cfd9.42655859.png,img_67e5762ce244d9.30859391.png,img_67e5762ce2b280.06456271.png,img_67e5762d00afe3.98811107.png', NULL, NULL, '2025-03-27 16:00:45', 4, 'rent', 0, ''),
(15, 'better', 'we are getting there', 2333.00, 'chalala', 'img_67e57cc24925c5.52321093.png', 'img_67e57cc24998a7.82337091.png,img_67e57cc24a0088.57533862.png,img_67e57cc24ad9e8.32051163.png,img_67e57cc24b4e02.75954274.png', NULL, NULL, '2025-03-27 16:28:50', 5, 'rent', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'omri', '$2y$10$YmLpbc9EB9s/nnF5wsaRMuRJAsG2K2LTTSb62w7p1PhQYCc7M3fXC', 'user', '2025-03-21 09:14:41'),
(3, 'james', '$2y$10$xHb7X4D18LvxP.pDTojtwu24p254De5CxpEHtFzCqssCh4n2fvBsG', 'user', '2025-03-22 14:36:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
