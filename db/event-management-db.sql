-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2023 at 09:32 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event-management-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_dashboard`
--

CREATE TABLE `admin_dashboard` (
  `setting_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `event_id`, `booking_date`, `status`) VALUES
(4, 4, 2, '2023-12-12 13:16:25', 'Attending'),
(14, 4, 1, '2023-12-12 15:23:08', 'Attending');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `venue` varchar(255) NOT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `start_date`, `end_date`, `venue`, `organizer_id`, `is_approved`, `image_path`, `is_featured`) VALUES
(1, 'Concert in the Park', 'Live music event in the city park', '2023-12-15 18:00:00', '2023-12-15 22:00:00', 'City Park', 1, 1, 'music_event.jpg', 1),
(2, 'Tech Conference 2023', 'An annual conference for tech enthusiasts', '2023-08-25 09:00:00', '2023-08-27 17:00:00', 'Tech Convention Center', 2, 1, 'Tech_Conference.jpg', 0),
(3, 'Art Exhibition', 'An art exhibition featuring local artists', '2023-07-10 10:00:00', '2023-07-15 18:00:00', 'Art Gallery', 3, 1, 'art.jpg', 0),
(14, 'test', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(15, 'test', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(16, 'test2', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(17, 'test 3', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(18, 'test 3', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(19, 'test 4', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 0, 'default.jpg', 0),
(20, 'test 4', 'test', '2023-12-25 09:04:00', '2023-12-26 21:04:00', 'river side', 4, 1, 'default.jpg', 0),
(23, 'This is a test event', 'this is a test event description', '2023-12-25 08:25:00', '2023-12-25 22:25:00', 'river side', 4, 0, 'image/event/uploads/default.png', 0),
(24, 'This is another test event', 'test event description', '2023-12-26 10:36:00', '2023-12-27 16:36:00', 'river side', 4, 1, 'default.jpg', 1),
(27, 'test event 101 2024', 'test event 101 2024 description', '2024-01-01 00:00:00', '2024-01-01 00:00:00', 'Island contry', 1, 1, '65788c3dd61f4_music_event_original.png', 0),
(30, 'christmas event 2023', 'christmas event 2023 description', '2023-12-25 00:00:00', '2023-12-25 00:00:00', 'Flat ground', 1, 1, '657890ab86596_music_event_original.png', 1),
(32, 'New year event 2024', 'New year event 2024 description', '2024-01-01 00:00:00', '2024-01-01 00:00:00', 'Flat ground', 1, 1, 'default.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_categories`
--

CREATE TABLE `event_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_categories`
--

INSERT INTO `event_categories` (`category_id`, `name`) VALUES
(1, 'Music'),
(2, 'Sports'),
(3, 'Conferences'),
(4, 'Workshops'),
(5, 'Art & Theater'),
(6, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `event_category_mapping`
--

CREATE TABLE `event_category_mapping` (
  `event_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_category_mapping`
--

INSERT INTO `event_category_mapping` (`event_id`, `category_id`) VALUES
(1, 1),
(2, 6),
(3, 5),
(14, NULL),
(15, NULL),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(23, 1),
(24, 1),
(27, 1),
(30, 1),
(32, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `email`, `password`, `gender`, `address`, `profile_picture`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'admin@example.com', '$2y$10$YGi323mXFwmbwYl/tg64v.TE9pQQ3hYgWHTkQN/1RH7VOptr24k36', NULL, NULL, 'default.png', 1, '2023-12-08 13:01:57', '2023-12-12 15:10:51'),
(2, 'stewi griffen', 'stewig', 'stewi_g@mailc.om', '$2y$10$B13wRDZbZn149Gqn4V71L.u.7lVGcAZS76dFEWinlZ7NFRBomndoe', 'Male', 'Test address', 'default.png', 0, '2023-12-10 02:27:40', '2023-12-12 16:39:32'),
(3, 'bryan griffen', 'briang', 'brian_g@mail.com', '$2y$10$o14HiLw68V/lD/Zq48fl4uoLXYoP/1xYqpM5mosVbIQeaq/zpYOdS', 'Male', 'Test1', 'default.png', 0, '2023-12-10 02:32:07', '2023-12-11 02:15:46'),
(4, 'louis lane', 'louisL', 'louis_l@mail.com', '$2y$10$OjFflXgNs1VUkS1GNWajPOLMy6d6JksjxXmXDKYtYLLSorrG6U/06', 'female', 'Test address', '4-65773ddeecc1f-girl-pic-1.jpg', 0, '2023-12-10 18:00:38', '2023-12-12 17:09:42'),
(5, 'james fuller', 'james_f', 'james_f@mail.com', '$2y$10$xQ7p.0hwSgamx0PcsXifuOIuqtvJPoM2uI59Y4kNaVXIO.vBkh25G', 'Male', 'Test address', NULL, 0, '2023-12-12 04:14:21', '2023-12-12 04:16:11'),
(6, 'robert dunn', 'robert_d', 'robert_d@mail.com', '$2y$10$Q3tjCSVBGbpX7sJtiJrKX.iuIGYn43xxyM4roEdIWXepTGsIoa84G', 'Male', 'test address', NULL, NULL, '2023-12-12 16:40:13', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_dashboard`
--
ALTER TABLE `admin_dashboard`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `event_categories`
--
ALTER TABLE `event_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `event_category_mapping`
--
ALTER TABLE `event_category_mapping`
  ADD KEY `event_id` (`event_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_dashboard`
--
ALTER TABLE `admin_dashboard`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `event_categories`
--
ALTER TABLE `event_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_category_mapping`
--
ALTER TABLE `event_category_mapping`
  ADD CONSTRAINT `event_category_mapping_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `event_category_mapping_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
