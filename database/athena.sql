-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 21, 2025 at 05:23 PM
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

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `admin_id`, `title`, `content`, `group_id`, `created_at`) VALUES
(1, 1, 'Scheduled Maintenance', 'The platform will be down for scheduled maintenance on July 25th from 2 AM to 3 AM. We apologize for any inconvenience.', NULL, '2025-07-21 02:30:00'),
(2, 1, 'New Feature: Group Chat!', 'We are excited to announce that group chat is now live! Start collaborating with your study group members in real-time.', NULL, '2025-07-21 02:32:00');

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

--
-- Dumping data for table `downloaded_notes`
--

INSERT INTO `downloaded_notes` (`download_id`, `note_id`, `user_id`, `downloaded_at`) VALUES
(1, 5, 1, '2025-07-21 02:15:00'),
(2, 7, 3, '2025-07-21 02:16:00'),
(3, 8, 6, '2025-07-21 02:17:00'),
(4, 1, 1, '2025-07-21 02:18:00'),
(5, 9, 1, '2025-07-21 02:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `members_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('member','moderator') DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`members_id`, `group_id`, `user_id`, `role`) VALUES
(1, 101, 2, 'moderator'),
(2, 101, 1, 'member'),
(3, 101, 3, 'member'),
(4, 101, 4, 'member'),
(5, 102, 3, 'moderator'),
(6, 102, 2, 'member'),
(7, 102, 5, 'member'),
(8, 103, 4, 'moderator'),
(9, 103, 2, 'member'),
(10, 103, 3, 'member'),
(11, 103, 5, 'member'),
(12, 104, 1, 'moderator'),
(15, 107, 6, 'moderator'),
(16, 107, 1, 'member'),
(17, 107, 4, 'member'),
(18, 107, 10, 'member'),
(19, 109, 8, 'moderator'),
(20, 109, 3, 'member'),
(21, 109, 7, 'member'),
(22, 110, 10, 'moderator'),
(23, 110, 1, 'member'),
(25, 107, 11, 'member'),
(26, 112, 11, 'moderator'),
(27, 112, 1, 'member'),
(29, 113, 1, 'moderator'),
(30, 113, 8, 'member'),
(31, 113, 5, 'member'),
(33, 101, 12, 'member'),
(36, 102, 12, 'member'),
(37, 104, 12, 'member'),
(38, 107, 12, 'member'),
(39, 113, 12, 'member'),
(41, 101, 6, 'member'),
(42, 102, 6, 'member'),
(43, 104, 6, 'member');

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

--
-- Dumping data for table `group_messages`
--

INSERT INTO `group_messages` (`message_id`, `group_id`, `user_id`, `content`, `time_stamp`) VALUES
(1, 107, 1, 'Has anyone solved the \"Two Sum\" problem yet?', '2025-07-21 02:20:00'),
(2, 107, 6, 'Yes, I have a solution in Python if you want to compare.', '2025-07-21 02:21:00'),
(3, 109, 7, 'I found a great documentary on Surrealism, I\'ll share the link!', '2025-07-21 02:25:00'),
(4, 112, 1, 'The market seems really volatile this week.', '2025-07-21 02:26:00'),
(5, 112, 11, 'Agreed. Sticking to blue-chip stocks for now. Less risk.', '2025-07-21 02:27:00');

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

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`note_id`, `group_id`, `user_id`, `title`, `file_path`, `upload_time`) VALUES
(1, 101, 2, 'Integration by Parts Cheatsheet', 'user_files/notes/calculus_integration.pdf', '2025-07-20 18:00:00'),
(2, 101, 4, 'Lecture 5 - Series and Sequences', 'user_files/notes/calculus_series.pdf', '2025-07-21 09:00:00'),
(3, 102, 3, 'Summary of Modern Indian History', 'user_files/notes/modern_history.docx', '2025-07-20 19:00:00'),
(4, 104, 1, 'Current Affairs - June 2025', 'user_files/notes/current_affairs_june.pdf', '2025-07-21 10:00:00'),
(5, 107, 6, 'Big O Notation Guide', 'user_files/notes/big_o_guide.pdf', '2025-07-21 01:30:00'),
(6, 107, 10, 'Quick Sort Implementation', 'user_files/notes/quicksort.txt', '2025-07-21 02:00:00'),
(7, 109, 8, 'Cubism vs. Surrealism', 'user_files/notes/art_movements.docx', '2025-07-21 02:05:00'),
(8, 110, 1, 'Common Network Ports', 'user_files/notes/network_ports.pdf', '2025-07-21 02:10:00'),
(9, 112, 11, 'Beginners Guide to Candlestick Charts', 'user_files/notes/candlestick_guide.pdf', '2025-07-21 02:12:00'),
(10, 113, 8, 'Rule of Thirds in Composition', 'user_files/notes/composition_rules.pdf', '2025-07-21 02:14:00');

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

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_by`, `target_type`, `target_id`, `reason`, `status`, `created_at`) VALUES
(1, 2, 'user', 9, 'This user is posting spam links in the group chat.', 'open', '2025-07-21 02:35:00'),
(2, 10, 'note', 7, 'This note contains incorrect information and is misleading.', 'open', '2025-07-21 02:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `study_group`
--

CREATE TABLE `study_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_group`
--

INSERT INTO `study_group` (`group_id`, `group_name`, `description`, `user_id`, `approved`, `created_at`) VALUES
(101, 'Calculus Study Group', 'Dedicated to mastering advanced calculus concepts and problem-solving.', 2, 1, '2025-07-20 14:00:00'),
(102, 'History Research Team', 'Collaborative group for historical research and essay writing.', 3, 1, '2025-07-20 15:00:00'),
(103, 'Physics Problem Solvers', 'Solving complex physics problems and preparing for exams.', 4, 1, '2025-07-20 16:00:00'),
(104, 'UPSC Aspirants 2026', 'A focused group for UPSC civil services examination preparation.', 1, 1, '2025-07-21 08:00:00'),
(105, 'Web Dev Beginners', 'A place for beginners to learn and share web development resources.', 5, 0, '2025-07-21 11:00:00'),
(107, 'Data Structures & Algo', 'A group for practicing DSA problems from LeetCode and HackerRank.', 6, 1, '2025-07-21 01:25:00'),
(108, 'Machine Learning Enthusiasts', 'Discussing papers, projects, and concepts in ML.', 7, 0, '2025-07-21 01:28:00'),
(109, 'Modern Art History', 'For students passionate about 20th-century art movements.', 8, 1, '2025-07-21 01:40:00'),
(110, 'Cybersecurity Beginners', 'A safe space to learn the fundamentals of cybersecurity and ethical hacking.', 10, 1, '2025-07-21 01:42:00'),
(111, 'Creative Writing Circle', 'Share your stories, poems, and get constructive feedback.', 2, 0, '2025-07-21 01:45:00'),
(112, 'Finance & Stock Market', 'Discussing investment strategies, market trends, and financial news.', 11, 1, '2025-07-21 01:55:00'),
(113, 'Photography Club', 'A place to share photos, techniques, and plan photo walks.', 1, 1, '2025-07-21 01:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `to_do`
--

CREATE TABLE `to_do` (
  `to_do_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('Open','in progress','done') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `to_do`
--

INSERT INTO `to_do` (`to_do_id`, `group_id`, `user_id`, `task`, `description`, `due_date`, `assigned_to`, `status`) VALUES
(1, 101, 1, 'Complete Chapter 3 exercises', NULL, '2025-07-25', NULL, 'Open'),
(2, 104, 1, 'Read The Hindu newspaper', NULL, '2025-07-22', NULL, 'in progress'),
(3, 101, 1, 'Review partial derivatives', NULL, '2025-07-28', NULL, 'Open'),
(4, 107, 6, 'Solve 5 easy array problems', 'From LeetCode top interview questions list.', '2025-07-28', NULL, 'Open'),
(5, 109, 8, 'Research Picasso\'s Blue Period', NULL, '2025-07-30', NULL, 'in progress'),
(6, 110, 1, 'Set up a firewall on a VM', 'Use UFW on a test Ubuntu server.', '2025-08-01', NULL, 'Open'),
(7, 101, 2, 'Review final exam topics', 'Focus on chapters 4-6.', '2025-07-25', NULL, 'done'),
(8, 112, 11, 'Analyze Q1 earnings for Tech Sector', NULL, '2025-07-29', NULL, 'Open');

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
(1, 'testuser', 'Test User', 'testuser@gmail.com', '1234567890', '123', 'active', 15, 'user_files/profile_pics/profile_687d3c1a07d20.jpeg', '2025-07-19 17:38:05'),
(2, 'jane_doe', 'Jane Doe', 'jane.doe@example.com', '9876543210', 'password123', 'active', 25, 'https://placehold.co/100x100/1e293b/ffffff?text=JD', '2025-07-20 10:00:00'),
(3, 'sam_jones', 'Sam Jones', 'sam.jones@example.com', '1122334455', 'password456', 'active', 5, 'https://placehold.co/100x100/1e293b/ffffff?text=SJ', '2025-07-20 11:30:00'),
(4, 'alex_smith', 'Alex Smith', 'alex.smith@example.com', '5566778899', 'password789', 'active', 30, 'https://placehold.co/100x100/1e293b/ffffff?text=AS', '2025-07-20 12:00:00'),
(5, 'emily_white', 'Emily White', 'emily.white@example.com', '9988776655', 'password101', 'active', 10, 'https://placehold.co/100x100/1e293b/ffffff?text=EW', '2025-07-21 09:00:00'),
(6, 'rahul_sharma', 'Rahul Sharma', 'rahul.sharma@example.com', '8877665544', 'pass123', 'active', 10, 'user_files/profile_pics/profile_687e569533e69.jpeg', '2025-07-21 01:20:00'),
(7, 'priya_patel', 'Priya Patel', 'priya.patel@example.com', '7766554433', 'pass456', 'active', 5, 'https://placehold.co/100x100/1e293b/ffffff?text=PP', '2025-07-21 01:22:00'),
(8, 'anika_verma', 'Anika Verma', 'anika.verma@example.com', '6655443322', 'pass789', 'active', 22, 'https://placehold.co/100x100/1e293b/ffffff?text=AV', '2025-07-21 01:35:00'),
(9, 'vikram_singh', 'Vikram Singh', 'vikram.singh@example.com', '5544332211', 'pass101', 'banned', 0, 'https://placehold.co/100x100/1e293b/ffffff?text=VS', '2025-07-21 01:36:00'),
(10, 'neha_chopra', 'Neha Chopra', 'neha.chopra@example.com', '4433221100', 'pass202', 'active', 18, 'https://placehold.co/100x100/1e293b/ffffff?text=NC', '2025-07-21 01:37:00'),
(11, 'arjun_mehta', 'Arjun Mehta', 'arjun.mehta@example.com', '3322110099', 'pass303', 'active', 40, 'https://placehold.co/100x100/1e293b/ffffff?text=AM', '2025-07-21 01:50:00'),
(12, 'testuser1', 'Test User 1', 'testuser1@gmail.com', '7591534682', '123', 'active', 0, 'user_files/profile_pics/profile_687d49bf8abaf.jpeg', '2025-07-21 01:25:09');

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
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `downloaded_notes`
--
ALTER TABLE `downloaded_notes`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `members_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `study_group`
--
ALTER TABLE `study_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `to_do`
--
ALTER TABLE `to_do`
  MODIFY `to_do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
