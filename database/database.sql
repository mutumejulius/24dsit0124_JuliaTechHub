-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 02:48 PM
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
-- Database: `julia_tech_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `broadcasts`
--

CREATE TABLE `broadcasts` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `broadcasts`
--

INSERT INTO `broadcasts` (`id`, `message`, `created_at`) VALUES
(1, 'Hello Innovators, this announcement serves to inform you that we have a webinar on 19th July 2026. The venue will be communicated soon.', '2026-03-18 12:24:58'),
(2, 'The hub wishes you a happy Eid holiday which will be on 20th March 2026', '2026-03-19 09:06:00'),
(3, 'Hello innovators,  Today we have a lecture on Dynamic Web Design. We shall have it live on zoom at 4pm', '2026-03-19 10:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `funding_applications`
--

CREATE TABLE `funding_applications` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `amount_requested` decimal(10,2) NOT NULL,
  `equity_offered` int(11) DEFAULT 0,
  `pitch_deck_url` varchar(255) DEFAULT NULL,
  `use_of_funds` text DEFAULT NULL,
  `status` enum('Pending','Under Review','Approved','Rejected') DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `funding_applications`
--

INSERT INTO `funding_applications` (`id`, `innovation_id`, `amount_requested`, `equity_offered`, `pitch_deck_url`, `use_of_funds`, `status`, `applied_at`, `admin_feedback`) VALUES
(1, 2, 2000.00, 8, 'uploads/decks/1773837910_Learning PHP, MySQL JavaScript - With jQuery, CSS HTML5 by Robin Nixon.pdf', 'I will use them to market my project', 'Rejected', '2026-03-18 12:45:10', 'Your application is lacking. Please add value to it');

-- --------------------------------------------------------

--
-- Table structure for table `funding_requests`
--

CREATE TABLE `funding_requests` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `amount_requested` decimal(15,2) NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('submitted','under_review','approved','disbursed','rejected') DEFAULT 'submitted',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `innovations`
--

CREATE TABLE `innovations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `stage` enum('Ideation','MVP','Market Ready','Scaling') DEFAULT 'Ideation',
  `logo_url` varchar(255) DEFAULT 'default_logo.png',
  `is_featured` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `innovations`
--

INSERT INTO `innovations` (`id`, `user_id`, `title`, `short_description`, `long_description`, `industry`, `stage`, `logo_url`, `is_featured`, `status`, `created_at`) VALUES
(1, 1, 'JULIA TECH HUB', 'I am creating a Tech Hub to bring together innovators, train them, and help them move their work to the next level.', NULL, 'EduTech', 'Ideation', 'default_logo.png', 1, 'pending', '2026-03-18 11:00:42'),
(2, 2, 'Online Tuition Payment System', 'The online tuition payment system will allow parents and students to pay school dues on the go without going to the bank or the school', NULL, 'FinTech', 'Ideation', 'default_logo.png', 1, 'approved', '2026-03-18 11:49:37'),
(3, 3, 'agricultural management app', 'booosting agricultural production', NULL, 'AgriTech', 'MVP', 'default_logo.png', 1, 'approved', '2026-03-18 16:14:57'),
(4, 4, 'Blood Donor Platform', 'The system will track records of blood donated by the public and the details of the donors.', NULL, 'HealthTech', 'Ideation', 'default_logo.png', 1, 'approved', '2026-03-19 09:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `media_url` varchar(255) NOT NULL,
  `media_type` enum('image','video') DEFAULT 'image',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `media_url`, `media_type`, `created_at`) VALUES
(1, 'Children In Tech', 'Julia Tech Hub has launched a national-wide campaign to engage children in tech innovation.', 'uploads/news/1774460039_children in tech.jfif', 'image', '2026-03-25 17:33:59'),
(2, 'Julia Tech Hub Moves to New Offices', 'The hub has officially moved its operations to new offices on The Innovation Building in Kampala.', 'uploads/news/1774516634_new offices.jfif', 'image', '2026-03-25 17:38:44'),
(3, 'Julia Tech Hub partners with National ICT Innovation Hub', 'Julia Tech Hub has announced its partnership with The National ICT Innovation Hub in Nakawa, Kampala.', 'uploads/news/1774516662_National ICT partnership.jfif', 'image', '2026-03-25 17:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply_message` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`, `reply_message`, `replied_at`) VALUES
(1, 2, 'Support Request from JACK MARTINS: Hello I want to know more on how I can have a clean good funding application', 'mentor_query', 1, '2026-03-18 13:19:58', 'Please do more research', '2026-03-18 13:37:42'),
(2, 2, 'Admin Feedback on \'Online Tuition Payment System\': Good idea', 'admin_msg', 0, '2026-03-18 14:24:38', NULL, NULL),
(3, 3, 'Admin Feedback on \'agricultural management app\': Good work', 'admin_msg', 0, '2026-03-18 16:17:43', NULL, NULL),
(4, 4, 'Support Request from ATUHAIRWE OLIVER: Hello Please advice me on my project', 'mentor_query', 1, '2026-03-19 09:03:13', 'Your Idea is good. Do more research and improve.', '2026-03-19 09:04:40'),
(5, 4, 'Admin Feedback on \'Blood Donor Management System\': Good Idea Keep on track', 'admin_msg', 0, '2026-03-19 09:05:17', NULL, NULL),
(6, 2, 'Support Request from JACK MARTINS: testing\r\n', 'mentor_query', 1, '2026-03-19 10:09:17', 'Good', '2026-03-19 10:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `link_url` varchar(255) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `title`, `category`, `link_url`, `file_url`, `uploaded_by`, `created_at`, `description`) VALUES
(3, 'ICT IN BUSINESS', 'Business', 'uploads/1773850311_ICT-in-Business.pdf', '', NULL, '2026-03-18 16:11:51', 'This will cover the impact of ICT in the business world'),
(4, 'INTRODUCTION TO JAVA', 'Coding', 'uploads/1773850763_introduction-to-java_programming.pdf', '', NULL, '2026-03-18 16:19:23', 'Here you will learn Java'),
(5, 'ICT IN MARKETING', 'Marketing', 'uploads/1773850811_ICT-in-Marketing.pdf', '', NULL, '2026-03-18 16:20:11', 'Here you will learn how to use ICT in marketing'),
(6, 'ICT IN LEGAL SECTOR', 'Legal', 'uploads/1773850854_ICT-in-Legal_sector.pdf', '', NULL, '2026-03-18 16:20:54', 'Focuses on ICT impact in Legal Sector');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','innovator','mentor') DEFAULT 'innovator',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'MUTUME JULIUS', 'mutumejuliusj256@gmail.com', '$2y$10$JUxDUFO0U0cJyJA91T5Y5.eL/cDwTvqtzCwtDbMVdd2DpPRAKPreK', 'admin', '2026-03-18 11:00:42'),
(2, 'JACK MARTINS', 'martins256@gmail.com', '$2y$10$LqnXR5DnEcDgGSKl8kNPWOeXUuAEV99hU2Se832ehINiUo7nbnXN.', 'innovator', '2026-03-18 11:49:37'),
(3, 'buyinza shalom', 'shalom@gmail.com', '$2y$10$c/VuN7TCQCwdLkJiHvv.SurkrBQedosXwgn0lj/B5OmQ4tC.UjybO', 'innovator', '2026-03-18 16:14:57'),
(4, 'ATUHAIRWE OLIVER', 'atuhairweoliver36@gmail.com', '$2y$10$TEbLJ8SOrFHAzKRVRTp1HubKI2OgFRWEe8AAjn9P1TzNAv4aI6ULa', 'innovator', '2026-03-19 09:01:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `broadcasts`
--
ALTER TABLE `broadcasts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `funding_applications`
--
ALTER TABLE `funding_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `innovation_id` (`innovation_id`);

--
-- Indexes for table `funding_requests`
--
ALTER TABLE `funding_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `innovation_id` (`innovation_id`);

--
-- Indexes for table `innovations`
--
ALTER TABLE `innovations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `broadcasts`
--
ALTER TABLE `broadcasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `funding_applications`
--
ALTER TABLE `funding_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `funding_requests`
--
ALTER TABLE `funding_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `innovations`
--
ALTER TABLE `innovations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `funding_applications`
--
ALTER TABLE `funding_applications`
  ADD CONSTRAINT `funding_applications_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `funding_requests`
--
ALTER TABLE `funding_requests`
  ADD CONSTRAINT `funding_requests_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `innovations`
--
ALTER TABLE `innovations`
  ADD CONSTRAINT `innovations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
