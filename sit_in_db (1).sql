-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 02:10 AM
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
-- Database: `sit_in_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Lab Rules Reminder', 'Please keep the laboratory clean and orderly at all times. Food and drinks are not allowed inside.', '2026-04-21 23:13:21'),
(2, 'CCS Sit-in Policy Update', 'Students must present their school ID before every sit-in session. No ID, no entry.', '2026-04-21 23:13:21'),
(3, 'sdsf', 'fsdfsd', '2026-04-22 00:05:29'),
(4, 'fdgdfgdgdf', 'dgdfgd', '2026-04-22 04:55:11'),
(5, 'dfgfdgdgdf', 'dfgfdgdf', '2026-04-22 04:55:21'),
(6, 'dfgdffg', 'dfgdfgdf', '2026-04-22 04:55:28'),
(7, 'dsffddfsd', 'dsfdssdf', '2026-04-22 06:21:39'),
(8, 'New Announcement', 'This is a new announcement.', '2026-04-22 11:11:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `type` enum('sitin','logout','announcement') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(1, 4, 'sitin', 'You have been recorded for sit-in at Lab 528 for Java.', 1, '2026-04-21 23:51:08'),
(2, 4, 'logout', 'You have been logged out from Lab 528 at Apr 22, 2026 01:51 AM.', 1, '2026-04-21 23:51:18'),
(3, 4, 'sitin', 'You have been recorded for sit-in at Lab 528 for Java.', 1, '2026-04-21 23:51:28'),
(4, 4, 'logout', 'You have been logged out from Lab 528 at Apr 22, 2026 02:00 AM.', 1, '2026-04-22 00:00:11'),
(5, 4, 'sitin', 'You have been recorded for sit-in at Lab 530 for Database.', 1, '2026-04-22 00:24:11'),
(6, 4, 'logout', 'You have been logged out from Lab 530 at Apr 22, 2026 07:36 AM.', 1, '2026-04-22 05:36:53'),
(7, 4, 'sitin', 'You have been recorded for sit-in at Lab 530 for Database.', 1, '2026-04-22 06:00:20'),
(8, 4, 'sitin', 'You have been recorded for sit-in at Lab 530 for Web Development.', 1, '2026-04-22 06:00:36'),
(9, 4, 'announcement', 'New announcement: dsffddfsd', 1, '2026-04-22 06:21:39'),
(10, 1, 'announcement', 'New announcement: dsffddfsd', 0, '2026-04-22 06:21:39'),
(11, 2, 'announcement', 'New announcement: dsffddfsd', 0, '2026-04-22 06:21:39'),
(12, 3, 'announcement', 'New announcement: dsffddfsd', 0, '2026-04-22 06:21:39'),
(13, 4, 'logout', 'You have been logged out from Lab 530 at Apr 22, 2026 09:30 AM.', 1, '2026-04-22 07:30:59'),
(14, 4, 'announcement', 'New announcement: New Announcement', 0, '2026-04-22 11:11:22'),
(15, 1, 'announcement', 'New announcement: New Announcement', 0, '2026-04-22 11:11:22'),
(16, 2, 'announcement', 'New announcement: New Announcement', 0, '2026-04-22 11:11:22'),
(17, 3, 'announcement', 'New announcement: New Announcement', 0, '2026-04-22 11:11:22'),
(18, 5, 'announcement', 'New announcement: New Announcement', 1, '2026-04-22 11:11:22'),
(19, 5, 'sitin', 'You have been recorded for sit-in at Lab 544 for Systems Analysis.', 1, '2026-04-22 11:11:41'),
(20, 5, 'logout', 'You have been logged out from Lab 544 at Apr 22, 2026 01:12 PM.', 1, '2026-04-22 11:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `lab` varchar(20) NOT NULL,
  `time_in` datetime DEFAULT current_timestamp(),
  `time_out` datetime DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `student_id`, `purpose`, `lab`, `time_in`, `time_out`, `feedback`) VALUES
(1, 4, 'Java', '528', '2026-04-22 07:51:08', '2026-04-22 07:51:18', 'done'),
(2, 4, 'Java', '528', '2026-04-22 07:51:28', '2026-04-22 08:00:11', 'weqweqwe'),
(3, 4, 'Database', '530', '2026-04-22 08:24:11', '2026-04-22 13:36:53', 'sfbsdkjfbsdjkfsdjkfbkjsdbfsjkdfbjksdbfdjkgb\r\ngdjkfghdjfg\r\ndghjdsfhg\r\ndgfhkgjhdfg\r\ndfgjkdsfgjhdf\r\ndfjgjhdfjhdf\r\ngjdhsdjghdfg\r\nskjdfjsdjhf\r\ndgjkjsdgsdjkg\r\nweweghjkdgsgd\r\njsdghjksdhgs'),
(4, 4, 'Database', '530', '2026-04-22 14:00:20', NULL, NULL),
(5, 4, 'Web Development', '530', '2026-04-22 14:00:36', '2026-04-22 15:30:59', NULL),
(6, 5, 'Systems Analysis', '544', '2026-04-22 19:11:41', '2026-04-22 19:12:08', 'Feedback here.');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `id_number` varchar(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `course` varchar(20) NOT NULL,
  `year_level` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `sessions_left` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `id_number`, `first_name`, `last_name`, `course`, `year_level`, `email`, `password`, `profile_picture`, `sessions_left`, `created_at`) VALUES
(1, '2021-00001', 'Maria', 'Santos', 'BSIT', 3, 'maria.santos@uc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 30, '2026-04-21 23:13:21'),
(2, '2021-00002', 'Juan', 'Dela Cruz', 'BSCS', 2, 'juan.delacruz@uc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 28, '2026-04-21 23:13:21'),
(3, '2022-00003', 'Ana', 'Reyes', 'BSIT', 1, 'ana.reyes@uc.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 30, '2026-04-21 23:13:21'),
(4, '12345678', 'Jane', 'Doe', 'BSIT', 1, 'jane@example.com', '$2y$10$iMyMD8tts0BqeHA2.o7yA.fb8eJo1NsPrLBXUC.mYMPIzYdfQt5vm', NULL, 25, '2026-04-21 23:28:17'),
(5, '87654321', 'John', 'Doe', 'BSIT', 3, 'john@example.com', '$2y$10$R.OPH/Pt.JCe2knbXxOayeug7mRZ1ZReTV8UBoMPw2RfW3jCC0ZGW', NULL, 29, '2026-04-22 11:10:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `id_number_2` (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD CONSTRAINT `sit_in_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
