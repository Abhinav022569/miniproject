-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 07:49 PM
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
(1, 'admin', '123', 'admin@athena.com');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `admin_id`, `title`, `content`, `user_id`, `created_at`) VALUES
(1, 1, 'Welcome to Athena!', 'Welcome to our new collaborative study platform. We hope you find it useful!', NULL, '2025-07-21 09:00:00'),
(2, 1, 'Scheduled Maintenance', 'The platform will be down for maintenance this Friday from 2 AM to 3 AM.', NULL, '2025-07-22 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `downloaded_notes`
--

CREATE TABLE `downloaded_notes` (
  `download_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `downloaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `downloaded_notes`
--

INSERT INTO `downloaded_notes` (`download_id`, `note_id`, `user_id`, `title`, `downloaded_at`) VALUES
(1, 1, 2, '', '2025-07-22 09:00:00'),
(2, 1, 3, '', '2025-07-22 09:05:00'),
(3, 3, 5, '', '2025-07-22 09:10:00'),
(4, 4, 9, '', '2025-07-22 09:15:00'),
(5, 5, 12, '', '2025-07-22 09:20:00'),
(8, 16, 1, 'Zodiac Quizzes on BuzzFeed.jpeg', '2025-07-22 21:50:07'),
(9, 17, 1, 'Snapchat sticker.jpeg', '2025-07-22 21:59:29'),
(10, 18, 1, 'receipt.pdf', '2025-07-22 22:17:35'),
(11, 19, 1, 'StudyGroups.jpeg', '2025-07-23 12:59:18');

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
(1, 101, 1, 'moderator'),
(2, 101, 2, 'member'),
(3, 101, 3, 'member'),
(4, 102, 3, 'moderator'),
(5, 102, 4, 'member'),
(6, 102, 5, 'member'),
(7, 103, 5, 'moderator'),
(8, 103, 6, 'member'),
(9, 103, 7, 'member'),
(10, 104, 8, 'moderator'),
(11, 104, 9, 'member'),
(12, 104, 10, 'member'),
(13, 105, 10, 'moderator'),
(14, 105, 11, 'member'),
(15, 105, 12, 'member'),
(16, 105, 13, 'member'),
(17, 101, 8, 'member'),
(56, 101, 14, 'member'),
(58, 119, 14, 'moderator'),
(60, 103, 5, 'moderator'),
(61, 102, 1, 'member'),
(62, 103, 1, 'member');

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
(1, 101, 1, 'Hey everyone, welcome to the Calculus Crew!', '2025-07-21 11:06:00'),
(2, 101, 2, 'Glad to be here! Ready to tackle some derivatives.', '2025-07-21 11:07:00'),
(3, 102, 3, 'Has anyone started the quantum mechanics homework?', '2025-07-21 11:12:00'),
(4, 102, 5, 'I have. It\'s pretty tough.', '2025-07-21 11:13:00'),
(5, 104, 8, 'Let\'s meet tomorrow to discuss the lab report.', '2025-07-21 11:22:00'),
(6, 104, 9, 'Sounds good. What time?', '2025-07-21 11:23:00'),
(25, 101, 1, 'Shared a file: <a href=\'log_download.php?note_id=16\' target=\'_blank\'>Zodiac Quizzes on BuzzFeed.jpeg</a>', '2025-07-22 21:50:06'),
(26, 101, 1, 'Shared a file: <a href=\'log_download.php?note_id=17\' target=\'_blank\'>Snapchat sticker.jpeg</a>', '2025-07-22 21:59:10'),
(27, 102, 1, 'Shared a file: <a href=\'log_download.php?note_id=18\' target=\'_blank\'>receipt.pdf</a>', '2025-07-22 22:17:33'),
(28, 119, 14, 'hello hi', '2025-07-23 12:57:10'),
(29, 119, 1, 'Shared a file: <a href=\'log_download.php?note_id=19\' target=\'_blank\'>StudyGroups.jpeg</a>', '2025-07-23 12:58:58'),
(30, 119, 1, 'send me notes', '2025-07-23 13:01:40'),
(31, 102, 1, 'Shared a file: <a href=\'log_download.php?note_id=20\' target=\'_blank\'>pub notice 22025.pdf</a>', '2025-07-23 22:51:56'),
(32, 119, 14, 'Shared a file: <a href=\'log_download.php?note_id=21\' target=\'_blank\'>StudyGroups.jpeg</a>', '2025-07-23 22:52:41'),
(33, 119, 14, 'Shared a file: <a href=\'log_download.php?note_id=22\' target=\'_blank\'>Zodiac Quizzes on BuzzFeed.jpeg</a>', '2025-07-23 22:52:46');

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
(1, 101, 1, 'Chapter 1: Limits', '/notes/calculus/chapter1.pdf', '2025-07-21 12:00:00'),
(2, 101, 2, 'Chapter 2: Derivatives', '/notes/calculus/chapter2.pdf', '2025-07-21 12:05:00'),
(3, 102, 3, 'Quantum Mechanics Basics', '/notes/physics/quantum_basics.docx', '2025-07-21 12:10:00'),
(4, 104, 8, 'Alkene Reactions', '/notes/chemistry/alkenes.pdf', '2025-07-21 12:15:00'),
(5, 105, 11, 'Analysis of Moby Dick', '/notes/literature/moby_dick_analysis.txt', '2025-07-21 12:20:00'),
(14, 101, 1, 'Zodiac Quizzes on BuzzFeed.jpeg', 'user_files/notes/note_687fb0495d5e46.56916139.jpeg', '2025-07-22 21:07:45'),
(15, 101, 1, 'Snapchat sticker.jpeg', 'user_files/notes/note_687fb99b2065d6.73452490.jpeg', '2025-07-22 21:47:31'),
(16, 101, 1, 'Zodiac Quizzes on BuzzFeed.jpeg', 'user_files/notes/note_687fba3603bd13.55382616.jpeg', '2025-07-22 21:50:06'),
(17, 101, 1, 'Snapchat sticker.jpeg', 'user_files/notes/note_687fbc56da8629.48106875.jpeg', '2025-07-22 21:59:10'),
(18, 102, 1, 'receipt.pdf', 'user_files/notes/note_687fc0a563a5e7.56291142.pdf', '2025-07-22 22:17:33'),
(19, 119, 1, 'StudyGroups.jpeg', 'user_files/notes/note_68808f3a1c5726.88244645.jpeg', '2025-07-23 12:58:58'),
(20, 102, 1, 'pub notice 22025.pdf', 'user_files/notes/note_68811a3428fb87.98058961.pdf', '2025-07-23 22:51:56'),
(21, 119, 14, 'StudyGroups.jpeg', 'user_files/notes/note_68811a6199ad28.83661633.jpeg', '2025-07-23 22:52:41'),
(22, 119, 14, 'Zodiac Quizzes on BuzzFeed.jpeg', 'user_files/notes/note_68811a66720d18.75639276.jpeg', '2025-07-23 22:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('open','resolved','review') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `user_id`, `target_id`, `group_id`, `reason`, `status`, `created_at`) VALUES
(6, 1, 2, 101, 'hthth', 'resolved', '2025-07-23 19:48:17'),
(7, 1, 4, 102, 'vergsbtbrstb', 'resolved', '2025-07-23 19:48:24'),
(8, 1, 14, 119, 'rg sehrs jsrjrsjrn', 'resolved', '2025-07-23 19:48:29'),
(9, 1, 2, 101, 'brbrbdbtnrnyn', 'resolved', '2025-07-23 19:49:15'),
(10, 1, 5, 102, 'strnrnryntyn', 'resolved', '2025-07-23 19:50:35');

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
(101, 'Calculus Crew', 'A group for students taking MATH-101.', 1, 1, '2025-07-21 11:05:00'),
(102, 'Physics Phantoms', 'Collaborating on advanced physics problems.', 3, 1, '2025-07-21 11:10:00'),
(103, 'History Buffs', 'Discussions and study sessions for HIST-202.', 5, 1, '2025-07-21 11:15:00'),
(104, 'Chem Champions', 'Working through organic chemistry.', 8, 1, '2025-07-21 11:20:00'),
(105, 'Literature Lovers', 'For deep dives into classic literature.', 10, 1, '2025-07-21 11:25:00'),
(119, 'UPSC', 'bbuevoenviseuv', 14, 1, '2025-07-23 12:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `to_do`
--

CREATE TABLE `to_do` (
  `to_do_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` varchar(200) NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('in progress','done') DEFAULT 'in progress'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `to_do`
--

INSERT INTO `to_do` (`to_do_id`, `user_id`, `task`, `due_date`, `status`) VALUES
(1, 1, 'Prepare presentation on derivatives', '2025-07-25', 'in progress'),
(2, 2, 'Complete practice problems for Ch. 2', '2025-07-24', 'in progress'),
(3, 3, 'Research Schrödinger\'s cat', '2025-07-26', 'done'),
(4, 8, 'Create flashcards for functional groups', '2025-07-23', 'in progress'),
(5, 10, 'Outline essay on The Great Gatsby', '2025-07-28', 'in progress');

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
  `status` enum('active','banned') DEFAULT 'active',
  `reputation_score` int(11) DEFAULT 0,
  `profile_pic` varchar(255) DEFAULT 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `name`, `email`, `phone_no`, `password`, `status`, `reputation_score`, `profile_pic`, `created_at`) VALUES
(1, 'testuser', 'Test User', 'john.doe@example.com', '1234567890', '123456', 'active', 11, 'user_files/profile_pics/profile_687fb97a9a5e5.jpeg', '2025-07-21 10:00:00'),
(2, 'jane_smith', 'Jane Smith', 'jane.smith@example.com', '0987654321', 'hashed_password_2', 'active', 5, 'https://placehold.co/100x100/e74c3c/ffffff?text=JS', '2025-07-21 10:05:00'),
(3, 'alice_jones', 'Alice Jones', 'alice.jones@example.com', '1122334455', 'hashed_password_3', 'active', 20, 'https://placehold.co/100x100/2ecc71/ffffff?text=AJ', '2025-07-21 10:10:00'),
(4, 'bob_brown', 'Bob Brown', 'bob.brown@example.com', '5566778899', 'hashed_password_4', 'banned', 0, 'https://placehold.co/100x100/f1c40f/ffffff?text=BB', '2025-07-21 10:15:00'),
(5, 'charlie_davis', 'Charlie Davis', 'charlie.davis@example.com', '1231231234', 'hashed_password_5', 'active', 15, 'https://placehold.co/100x100/9b59b6/ffffff?text=CD', '2025-07-21 10:20:00'),
(6, 'diana_miller', 'Diana Miller', 'diana.miller@example.com', '4564564567', 'hashed_password_6', 'active', 8, 'https://placehold.co/100x100/1abc9c/ffffff?text=DM', '2025-07-21 10:25:00'),
(7, 'ethan_wilson', 'Ethan Wilson', 'ethan.wilson@example.com', '7897897890', 'hashed_password_7', 'banned', 2, 'https://placehold.co/100x100/e67e22/ffffff?text=EW', '2025-07-21 10:30:00'),
(8, 'fiona_moore', 'Fiona Moore', 'fiona.moore@example.com', '1472583690', 'hashed_password_8', 'active', 30, 'https://placehold.co/100x100/34495e/ffffff?text=FM', '2025-07-21 10:35:00'),
(9, 'george_taylor', 'George Taylor', 'george.taylor@example.com', '3692581470', 'hashed_password_9', 'active', 12, 'https://placehold.co/100x100/95a5a6/ffffff?text=GT', '2025-07-21 10:40:00'),
(10, 'hannah_anderson', 'Hannah Anderson', 'hannah.anderson@example.com', '2583691470', 'hashed_password_10', 'active', 25, 'https://placehold.co/100x100/d35400/ffffff?text=HA', '2025-07-21 10:45:00'),
(11, 'ian_thomas', 'Ian Thomas', 'ian.thomas@example.com', '1593572468', 'hashed_password_11', 'active', 18, 'https://placehold.co/100x100/c0392b/ffffff?text=IT', '2025-07-21 10:50:00'),
(12, 'jenna_jackson', 'Jenna Jackson', 'jenna.jackson@example.com', '3571592468', 'hashed_password_12', 'active', 22, 'https://placehold.co/100x100/8e44ad/ffffff?text=JJ', '2025-07-21 10:55:00'),
(13, 'kevin_white', 'Kevin White', 'kevin.white@example.com', '9517538246', 'hashed_password_13', 'active', 3, 'https://placehold.co/100x100/2c3e50/ffffff?text=KW', '2025-07-21 11:00:00'),
(14, 'abhinav', 'Abhinav R Nair', 'abhinavrnair8413888957@gmail.com', '9586235148', '123456', 'active', 2, 'user_files/profile_pics/profile_68808e08e2f95.jpeg', '2025-07-23 12:53:21');

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
  ADD KEY `group_id` (`user_id`);

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
  ADD KEY `user_by` (`user_id`),
  ADD KEY `target_id` (`target_id`),
  ADD KEY `reports_ibfk_3` (`group_id`);

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
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `members_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `study_group`
--
ALTER TABLE `study_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `to_do`
--
ALTER TABLE `to_do`
  MODIFY `to_do_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

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
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`target_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `study_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `study_group`
--
ALTER TABLE `study_group`
  ADD CONSTRAINT `study_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `to_do`
--
ALTER TABLE `to_do`
  ADD CONSTRAINT `to_do_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
