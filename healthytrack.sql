-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 04:39 PM
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
-- Database: `healthytrack`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `action` text NOT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `log_time`) VALUES
('L001', 'U011', 'User logged in', '2025-03-29 03:10:57'),
('L002', 'U011', 'User logged out', '2025-03-29 03:11:45'),
('L003', 'U011', 'User logged in', '2025-03-29 03:14:12'),
('L004', 'U011', 'User logged out', '2025-03-29 03:14:36'),
('L005', 'U011', 'User logged in', '2025-03-29 03:14:38'),
('L006', 'U011', 'User added a Preset meal log', '2025-03-29 03:14:41'),
('L007', 'U011', 'User logged out', '2025-03-29 03:14:47'),
('L008', 'U011', 'User logged in', '2025-03-29 03:14:50'),
('L009', 'U011', 'User added a Preset meal log', '2025-03-29 03:14:59'),
('L010', 'U011', 'User logged in', '2025-03-29 03:18:44'),
('L011', 'U011', 'User logged out', '2025-03-29 03:18:52'),
('L012', 'U011', 'User logged in', '2025-03-29 12:31:18'),
('L013', 'U011', 'User logged out', '2025-03-29 13:19:24'),
('L014', 'U011', 'User logged in', '2025-03-29 13:19:26'),
('L015', 'U011', 'User logged out', '2025-03-29 13:20:41'),
('L016', 'U011', 'User logged in', '2025-03-29 13:21:04'),
('L017', 'U011', 'User logged out', '2025-03-29 13:22:32'),
('L018', 'U011', 'User logged in', '2025-03-29 13:23:13'),
('L019', 'U011', 'User logged out', '2025-03-29 13:24:26'),
('L020', 'U011', 'User logged in', '2025-03-29 13:26:34'),
('L021', 'U011', 'User logged out', '2025-03-29 13:29:18'),
('L022', 'U002', 'User updated settings', '2025-03-25 02:05:00'),
('L023', 'U003', 'User started cardio workout', '2025-03-25 02:15:30'),
('L024', 'U005', 'User added a strength workout', '2025-03-25 02:20:45'),
('L025', 'U011', 'User logged in', '2025-03-30 14:29:43'),
('L026', 'U011', 'User logged out', '2025-03-30 14:30:21');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` varchar(10) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `username`, `password`, `created_at`) VALUES
('A001', 'Admin One', 'admin1@example.com', 'admin1', 'adminpass', '2025-03-28 03:18:55'),
('A002', '222', '222@gmail.com', '222', '222', '2025-03-19 06:33:24'),
('A003', 'Alex Carter', 'alex.carter@healthytrack.com', 'alexc', 'newSecurePass789', '2025-03-25 01:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `cus_cardioworkout`
--

CREATE TABLE `cus_cardioworkout` (
  `CW_id` varchar(10) NOT NULL,
  `E_id` varchar(10) NOT NULL,
  `CW_minutes` int(11) NOT NULL,
  `CW_calories` int(11) NOT NULL,
  `CW_date` date NOT NULL,
  `user_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cus_cardioworkout`
--

INSERT INTO `cus_cardioworkout` (`CW_id`, `E_id`, `CW_minutes`, `CW_calories`, `CW_date`, `user_id`) VALUES
('CW001', 'E001', 30, 250, '2025-03-20', 'U001'),
('CW002', 'E002', 20, 300, '2025-03-21', 'U002'),
('CW003', 'E001', 25, 220, '2025-03-16', 'U001'),
('CW004', 'E002', 15, 280, '2025-03-17', 'U002'),
('CW005', 'E001', 30, 250, '2025-03-22', 'U001'),
('CW006', 'E001', 35, 270, '2025-03-22', 'U002'),
('CW007', 'E001', 40, 290, '2025-03-23', 'U003'),
('CW008', 'E001', 25, 230, '2025-03-23', 'U004'),
('CW009', 'E002', 20, 300, '2025-03-22', 'U005'),
('CW010', 'E002', 22, 310, '2025-03-23', 'U001'),
('CW011', 'E002', 35, 330, '2025-03-25', 'U002'),
('CW012', 'E001', 40, 280, '2025-03-25', 'U004');

-- --------------------------------------------------------

--
-- Table structure for table `cus_strengthworkout`
--

CREATE TABLE `cus_strengthworkout` (
  `SW_id` varchar(10) NOT NULL,
  `E_id` varchar(10) NOT NULL,
  `SW_kg` int(11) NOT NULL,
  `SW_sets` int(11) NOT NULL,
  `SW_reps` int(11) NOT NULL,
  `SW_date` date NOT NULL,
  `user_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cus_strengthworkout`
--

INSERT INTO `cus_strengthworkout` (`SW_id`, `E_id`, `SW_kg`, `SW_sets`, `SW_reps`, `SW_date`, `user_id`) VALUES
('SW001', 'E003', 50, 3, 12, '2025-03-20', 'U001'),
('SW002', 'E004', 15, 4, 10, '2025-03-21', 'U002'),
('SW003', 'E003', 55, 3, 10, '2025-03-16', 'U003'),
('SW004', 'E004', 18, 4, 8, '2025-03-17', 'U004'),
('SW005', 'E003', 50, 3, 12, '2025-03-22', 'U001'),
('SW006', 'E003', 55, 3, 10, '2025-03-22', 'U002'),
('SW007', 'E003', 60, 4, 10, '2025-03-23', 'U003'),
('SW008', 'E004', 15, 4, 10, '2025-03-22', 'U004'),
('SW009', 'E004', 16, 4, 8, '2025-03-23', 'U005'),
('SW010', 'E003', 50, 4, 10, '2025-03-25', 'U005'),
('SW011', 'E004', 0, 3, 20, '2025-03-25', 'U002');

-- --------------------------------------------------------

--
-- Table structure for table `diet_plans`
--

CREATE TABLE `diet_plans` (
  `diet_id` varchar(10) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `calories` int(11) NOT NULL,
  `diet_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet_plans`
--

INSERT INTO `diet_plans` (`diet_id`, `plan_name`, `description`, `calories`, `diet_type`) VALUES
('DP001', 'Keto Plan', 'Low carb, high fat.', 1500, 'low_carb'),
('DP002', 'Vegetarian Plan', 'Plant-based balanced diet.', 1800, 'vegetarian'),
('DP003', 'High-Protein Plan', 'Protein rich diet.', 2000, 'high_protein'),
('DP004', 'Mediterranean Plan', 'Rich in fruits, vegetables, whole grains and healthy fats.', 2100, 'balanced');

-- --------------------------------------------------------

--
-- Table structure for table `exercise`
--

CREATE TABLE `exercise` (
  `E_id` varchar(10) NOT NULL,
  `E_name` varchar(100) NOT NULL,
  `E_category` varchar(50) NOT NULL,
  `E_target` varchar(50) NOT NULL,
  `E_difficulty` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercise`
--

INSERT INTO `exercise` (`E_id`, `E_name`, `E_category`, `E_target`, `E_difficulty`) VALUES
('E001', 'Jogging', 'Cardiovascular', 'Legs', '3'),
('E002', 'Sprinting', 'Cardiovascular', 'Legs', '7'),
('E003', 'Squat', 'Strength', 'Legs', '6'),
('E004', 'Bicep Curl', 'Strength', 'Arms', '5'),
('E005', 'Plank', 'Cardiovascular', 'Abdominals', '2'),
('E006', 'Burpees', 'Cardiovascular', 'Full Body', '8');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` varchar(10) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `message`, `submitted_at`) VALUES
('FB001', 'U001', 'Great app!', '2025-03-28 03:18:55'),
('FB002', 'U002', 'Could use more workout plans.', '2025-03-28 03:18:55'),
('FB003', 'U003', 'Loving the new updates.', '2025-03-28 03:18:55'),
('FB004', 'U002', 'Loving the new workout challenges!', '2025-03-25 02:30:00'),
('FB005', 'U004', 'Would appreciate more detailed exercise instructions.', '2025-03-25 02:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(10) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `height` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `fitness_level` varchar(50) NOT NULL,
  `goal` varchar(50) DEFAULT NULL,
  `goal_value` int(11) DEFAULT NULL,
  `diet` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 1,
  `role` enum('new','long_time') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `username`, `password`, `dob`, `height`, `weight`, `gender`, `fitness_level`, `goal`, `goal_value`, `diet`, `created_at`, `status`, `role`) VALUES
('U001', 'Alice Johnson', 'alice@example.com', 'alicej', 'password1', '1990-01-15', 165, 60, 'female', '4', 'lose_weight', 5, 'balanced', '2025-03-28 03:18:55', 1, 'new'),
('U002', 'Bob Smith', 'bob@example.com', 'bobsmith', 'password2', '1985-05-20', 175, 80, 'male', '2', 'gain_muscle', 10, 'high_protein', '2025-03-28 03:18:55', 1, 'new'),
('U003', 'Carol Williams', 'carol@example.com', 'carolw', 'password3', '1992-08-10', 160, 55, 'female', '7', 'maintain', NULL, 'vegetarian', '2025-03-28 03:18:55', 1, 'new'),
('U004', 'David Brown', 'david@example.com', 'davidb', 'password4', '1980-12-01', 180, 85, 'male', '5', 'gain_muscle', 15, 'balanced', '2025-03-28 03:18:55', 0, 'new'),
('U005', 'Eve Davis', 'eve@example.com', 'eved', 'password5', '1995-07-30', 170, 65, 'female', '2', 'lose_weight', 3, 'low_carb', '2025-03-28 03:18:55', 1, 'new'),
('U010', 'Timothy', 'Timothy149@gmail.com', 'Timothyok', 'Timothy', '2025-03-12', 22, 22, 'male', '1', '', NULL, NULL, '2025-03-07 01:51:44', 1, 'new'),
('U011', '333', 'Timothy149@gmail.com', '333', '333', '2025-03-06', 333, 222, 'male', '3', '', NULL, NULL, '2025-03-07 05:53:12', 1, 'new'),
('U012', 'Timothy', 'Timothy149@gmail.com', 'Timothy', 'Timothy', '2025-02-26', 222, 222, 'male', '1', '', NULL, NULL, '2025-03-07 06:59:42', 1, 'new'),
('U013', 'Timothy1170', 'Timothy178@gmail.com', 'Timothy12', '222', '2025-02-25', 222, 220, 'female', '1', '', NULL, NULL, '2025-03-07 07:51:48', 1, 'new'),
('U014', '111', 'test12@gmail.comt', '111', '111', '2025-03-08', 111, 100, 'male', '1', '', NULL, NULL, '2025-03-07 23:19:17', 1, 'new'),
('U015', '', 'test12@gmail.com', 'root', 'Testing', '0000-00-00', 0, 0, '', '1', 'gain_muscle', 70, 'balanced', '2025-03-07 23:24:57', 1, 'new'),
('U016', 'HelloWorld', 'test1002@gmail.com', 'zzzzz', 'zzz', '2025-03-05', 170, 50, 'male', '3', 'gain_muscle', 60, 'balanced', '2025-03-08 01:35:55', 1, 'new'),
('U017', 'William Wong', 'williamteam10@gmail.com', 'Guava', 'WILLIAM10', '2005-10-30', 171, 60, 'male', '5', 'gain_muscle', 70, 'balanced', '2025-03-11 16:56:42', 1, 'new'),
('U018', 'test', 'test@gmail.com', 'test', 'test', '2005-03-02', 123, 55, 'male', '1', 'lose_weight', -24, 'vegetarian', '2025-03-19 05:39:58', 1, 'new'),
('U019', 'Fiona Green', 'fiona.green@example.com', 'fionag', 'passFiona6', '1993-03-12', 168, 64, 'female', '3', 'maintain', NULL, NULL, '2025-03-25 01:30:00', 1, 'new'),
('U020', 'George King', 'george.king@example.com', 'georgek', 'passGeorge7', '1987-11-05', 180, 82, 'male', '7', 'gain_muscle', NULL, NULL, '2025-03-25 01:35:00', 1, 'new');

-- --------------------------------------------------------

--
-- Table structure for table `user_diet_logs`
--

CREATE TABLE `user_diet_logs` (
  `log_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `diet_id` varchar(10) DEFAULT NULL,
  `meal_type` enum('breakfast','lunch','dinner') DEFAULT NULL,
  `meal_description` text NOT NULL,
  `calories` int(11) NOT NULL,
  `log_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_diet_logs`
--

INSERT INTO `user_diet_logs` (`log_id`, `user_id`, `diet_id`, `meal_type`, `meal_description`, `calories`, `log_date`) VALUES
('DL102', 'U003', NULL, 'breakfast', 'Custom breakfast: banana smoothie with oats', 370, '2025-03-25'),
('DL107', 'U003', NULL, 'lunch', 'Custom lunch: quinoa and roasted vegetables bowl', 510, '2025-03-25'),
('DL108', 'U011', 'DP001', 'breakfast', '', 1500, '2025-03-29'),
('DL109', 'U011', 'DP002', 'lunch', '', 1800, '2025-03-29');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `weight` float NOT NULL,
  `steps` int(11) NOT NULL,
  `water` float NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `weight`, `steps`, `water`, `date`) VALUES
(1, 'U001', 60, 10000, 2, '2025-03-20 00:00:00'),
(2, 'U002', 80, 8000, 1.5, '2025-03-20 00:00:00'),
(3, 'U003', 55, 12000, 2.2, '2025-03-20 00:00:00'),
(4, 'U005', 65, 9000, 1.8, '2025-03-21 00:00:00'),
(6, 'U019', 64.5, 9200, 2.1, '2025-03-24 16:00:00'),
(7, 'U020', 81, 8800, 1.9, '2025-03-24 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `plan_id` varchar(10) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `days` int(11) NOT NULL DEFAULT 0,
  `weeks` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`plan_id`, `plan_name`, `description`, `difficulty`, `days`, `weeks`) VALUES
('WP001', 'Beginner Full Body', 'A full body workout for beginners.', 'Beginner', 3, 4),
('WP002', 'Advanced Strength', 'Intense strength training routine.', 'Advanced', 4, 6),
('WP003', 'Core & Cardio', 'A plan focusing on strengthening your core while boosting cardiovascular health.', 'Intermediate', 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `workout_plan_exercises`
--

CREATE TABLE `workout_plan_exercises` (
  `plan_id` varchar(10) NOT NULL,
  `E_id` varchar(10) NOT NULL,
  `day_of_week` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plan_exercises`
--

INSERT INTO `workout_plan_exercises` (`plan_id`, `E_id`, `day_of_week`) VALUES
('WP001', 'E001', 'Friday'),
('WP001', 'E001', 'Monday'),
('WP001', 'E002', 'Friday'),
('WP001', 'E002', 'Wednesday'),
('WP001', 'E003', 'Sunday'),
('WP001', 'E003', 'Wednesday'),
('WP001', 'E004', 'Monday'),
('WP001', 'E004', 'Sunday'),
('WP002', 'E001', 'Monday'),
('WP002', 'E001', 'Thursday'),
('WP002', 'E002', 'Monday'),
('WP002', 'E002', 'Tuesday'),
('WP002', 'E003', 'Tuesday'),
('WP002', 'E003', 'Wednesday'),
('WP002', 'E004', 'Thursday'),
('WP002', 'E004', 'Wednesday'),
('WP003', 'E001', 'Saturday'),
('WP003', 'E005', 'Friday'),
('WP003', 'E005', 'Tuesday'),
('WP003', 'E006', 'Thursday');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cus_cardioworkout`
--
ALTER TABLE `cus_cardioworkout`
  ADD PRIMARY KEY (`CW_id`),
  ADD KEY `E_id` (`E_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cus_strengthworkout`
--
ALTER TABLE `cus_strengthworkout`
  ADD PRIMARY KEY (`SW_id`),
  ADD KEY `fk_strength_user` (`user_id`),
  ADD KEY `fk_strength_exercise` (`E_id`);

--
-- Indexes for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD PRIMARY KEY (`diet_id`);

--
-- Indexes for table `exercise`
--
ALTER TABLE `exercise`
  ADD PRIMARY KEY (`E_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_feedback_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_diet_logs`
--
ALTER TABLE `user_diet_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_diet_logs_fk_diet` (`diet_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_progress` (`user_id`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `workout_plan_exercises`
--
ALTER TABLE `workout_plan_exercises`
  ADD PRIMARY KEY (`plan_id`,`E_id`,`day_of_week`),
  ADD KEY `E_id` (`E_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cus_cardioworkout`
--
ALTER TABLE `cus_cardioworkout`
  ADD CONSTRAINT `fk_cus_cardioworkout_exercise` FOREIGN KEY (`E_id`) REFERENCES `exercise` (`E_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cus_cardioworkout_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cus_strengthworkout`
--
ALTER TABLE `cus_strengthworkout`
  ADD CONSTRAINT `fk_cus_strengthworkout_exercise` FOREIGN KEY (`E_id`) REFERENCES `exercise` (`E_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cus_strengthworkout_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_diet_logs`
--
ALTER TABLE `user_diet_logs`
  ADD CONSTRAINT `user_diet_logs_fk_diet` FOREIGN KEY (`diet_id`) REFERENCES `diet_plans` (`diet_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_diet_logs_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `fk_user_progress` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plan_exercises`
--
ALTER TABLE `workout_plan_exercises`
  ADD CONSTRAINT `fk_workout_plan_exercises_exercise` FOREIGN KEY (`E_id`) REFERENCES `exercise` (`E_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_workout_plan_exercises_plan` FOREIGN KEY (`plan_id`) REFERENCES `workout_plans` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
