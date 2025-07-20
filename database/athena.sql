-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 07:13 AM
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
-- Database: `athena`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_name`, `password`, `email`) VALUES
(1, 'testadmin', '123', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `downloaded_notes`
--

CREATE TABLE `downloaded_notes` (
  `download_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `downloaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `members_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('member','moderator') DEFAULT 'member',
  `status` enum('pending','joined') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_messages`
--

CREATE TABLE `group_messages` (
  `message_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `time_stamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `note_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_by` int(11) NOT NULL,
  `target_type` enum('user','message','note') NOT NULL,
  `target_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('open','resolved','review') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_group`
--

CREATE TABLE `study_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `to_do`
--

CREATE TABLE `to_do` (
  `to_do_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(200) NOT NULL,
  `due_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('Open','in progress','done') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','banned','Suspected') DEFAULT 'active',
  `reputation_score` int(11) DEFAULT 0,
  `profile_pic` varchar(255) DEFAULT 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `name`, `email`, `phone_no`, `password`, `status`, `reputation_score`, `profile_pic`, `created_at`) VALUES
(1, 'testuser', '', 'testuser@gmail.com', '1234567890', '123', 'active', 0, 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP', '2025-07-19 17:38:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `downloaded_notes`
--
ALTER TABLE `downloaded_notes`
  ADD PRIMARY KEY (`download_id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`members_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_by` (`user_by`);

--
-- Indexes for table `study_group`
--
ALTER TABLE `study_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `to_do`
--
ALTER TABLE `to_do`
  ADD PRIMARY KEY (`to_do_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_no` (`phone_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `downloaded_notes`
--
ALTER TABLE `downloaded_notes`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `members_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_group`
--
ALTER TABLE `study_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `to_do`
--
ALTER TABLE `to_do`
  MODIFY `to_do_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`);

--
-- Constraints for table `downloaded_notes`
--
ALTER TABLE `downloaded_notes`
  ADD CONSTRAINT `downloaded_notes_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`note_id`),
  ADD CONSTRAINT `downloaded_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`),
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`),
  ADD CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `study_group`
--
ALTER TABLE `study_group`
  ADD CONSTRAINT `study_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `to_do`
--
ALTER TABLE `to_do`
  ADD CONSTRAINT `to_do_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`),
  ADD CONSTRAINT `to_do_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `to_do_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
