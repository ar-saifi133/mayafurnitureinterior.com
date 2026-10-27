-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 02, 2026 at 08:43 AM
-- Server version: 10.6.19-MariaDB-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mayafurniture_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `login_ip` varchar(45) DEFAULT NULL,
  `login_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `phone`, `image`, `login_ip`, `login_date`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$CMjIJLraPasaKfrzrvSeG.AqA38t20q/olGJFlZqt0qa.yqzGMCw.', NULL, NULL, '106.219.162.233', '2026-05-08 09:24:28');

-- --------------------------------------------------------

--
-- Table structure for table `contact_queries`
--

CREATE TABLE `contact_queries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_queries`
--

INSERT INTO `contact_queries` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'testing contact page query', 'test@test.com', '9797979777', 'test', '2026-05-07 12:38:18'),
(2, 'Imran Ahmad', 'imran@ahmad.com', '9797979777', 'imran ahmad not realistic name.', '2026-05-07 12:44:29'),
(3, 'test', 'test@test.com', 'test', 'test', '2026-05-07 12:49:36'),
(4, 'tet', 'tasleem@go.co', 'test', 'test', '2026-05-07 12:50:54'),
(5, 'testing on 08-5', 'test@test.com', '1223334444', 'test', '2026-05-08 07:25:09'),
(6, 'Testing the email function', 'test@test.com', '9797979777', 'Testing the email function', '2026-05-08 09:24:18'),
(7, 'test', 'test@test.com', '1223334444', 'test', '2026-05-08 09:30:24'),
(8, 'Testing', 'test@test.com', '1223334444', 'test', '2026-05-08 09:42:43'),
(9, 'Testing', 'test@test.com', '9797979777', 'test', '2026-05-08 09:48:22'),
(10, 'test on', 'test@test.com', '9572306599', 'test on', '2026-05-08 10:02:45'),
(11, 'test user', 'test@test.com', '9797979777', 'test user', '2026-05-08 10:05:54'),
(12, 'Testing', 'test@test.com', '8787878787', 'test', '2026-05-08 10:06:53'),
(13, 'Mr. Zeeshan', 'test@test.com', '9797979777', 'test user', '2026-05-08 10:10:55'),
(14, 'Jo Riggs', 'joriggsvideo2@gmail.com', '7845198303', 'Hi,\r\n\r\nI just visited mayafurnitureinterior.com and wondered if you\'ve ever considered an impactful video to advertise your business? Our videos can generate impressive results on both your website and across social media.\r\n\r\nOur videos cost just $195 (USD) for a 30 second video ($239 for 60 seconds) and include a full script, voice-over and video.\r\n\r\nI can show you some previous videos we\'ve done if you want me to send some over. Let me know if you\'re interested in seeing samples of our previous work.\r\n\r\nRegards,\r\nJo', '2026-07-24 22:34:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_queries`
--
ALTER TABLE `contact_queries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_queries`
--
ALTER TABLE `contact_queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
