-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2025 at 03:14 PM
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
-- Database: `pafsr_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_import_history`
--

CREATE TABLE `data_import_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `import_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `records_imported` int(11) NOT NULL,
  `status` enum('success','partial','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `import_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_level` int(11) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `status` enum('Active','Dropped','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `course`, `year_level`, `section`, `status`, `created_at`) VALUES
('1111111111', 'jann', 'sumalinog', 'meljohnsumalinog@gmail.com', 'BSIT', 1, 'A-10', 'Active', '2025-03-30 03:54:11'),
('1111111112', 'mavs', 'cutanda', 'maverickcutanda73@gmail.com', 'BSIT', 3, 'A-10', 'Inactive', '2025-04-01 09:14:55'),
('1231231231', 'maw', 'palermo', 'meljohnsumalinogg@gmail.com', 'BSIT', 2, 'A-13', 'Active', '2025-04-01 09:14:25'),
('2222222222', 'razz', 'maw', 'jannnnn.sumalinog@gmail.com', 'BSIT', 3, 'A-10', 'Active', '2025-03-30 03:57:15'),
('2323232323', 'gerald', 'laspona', 'geraldlaspona@gmail.com', 'BSIT', 2, 'A-10', 'Active', '2025-04-01 09:49:24');

-- --------------------------------------------------------

--
-- Table structure for table `student_assessments`
--

CREATE TABLE `student_assessments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assessment_type` enum('quiz','exam','assignment','project') NOT NULL,
  `assessment_date` date NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `max_score` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assessments`
--

INSERT INTO `student_assessments` (`id`, `student_id`, `assessment_type`, `assessment_date`, `score`, `max_score`, `created_at`, `updated_at`) VALUES
(1, 1111111111, 'quiz', '2025-04-01', 10.00, 10.00, '2025-04-01 08:55:29', '2025-04-01 08:55:29'),
(3, 1111111112, 'exam', '2025-03-31', 20.00, 20.00, '2025-04-01 09:23:05', '2025-04-01 09:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `student_id`, `attendance_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1111111111, '2025-04-01', 'present', '2025-04-01 08:55:29', '2025-04-01 08:55:29'),
(3, 1111111112, '2025-04-01', 'present', '2025-04-01 09:23:05', '2025-04-01 09:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `student_participation`
--

CREATE TABLE `student_participation` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `participation_date` date NOT NULL,
  `level` int(11) NOT NULL CHECK (`level` between 1 and 5),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_participation`
--

INSERT INTO `student_participation` (`id`, `student_id`, `participation_date`, `level`, `notes`, `created_at`, `updated_at`) VALUES
(2, 1111111111, '2025-04-01', 1, '', '2025-04-01 09:15:47', '2025-04-01 09:15:47'),
(3, 1111111112, '2025-04-01', 5, '', '2025-04-01 09:23:05', '2025-04-01 09:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_verification_history`
--

CREATE TABLE `teacher_verification_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `action` enum('verified','rejected') NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_verification_history`
--

INSERT INTO `teacher_verification_history` (`id`, `user_id`, `first_name`, `last_name`, `email`, `action`, `action_date`) VALUES
(1, 31, 'nadine', 'palermo', 'admin22222222@school.com', 'verified', '2025-04-01 10:26:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `phone_number` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT 'user',
  `verified` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `first_name`, `last_name`, `department`, `employee_id`, `phone_number`, `username`, `password`, `email`, `role`, `verified`) VALUES
(27, 'mavs', 'cutanda', 'Information technology', '516162334634', '09123131124', 'mave12', '$2y$10$.PF2HS4ypDcS.wYJ1HqHAukW15sUyJcytKyLBldK9Hnyhlvz8ax4C', 'maverickcutanda73@gmail.com', 'teacher', 'verified'),
(28, 'jann', 'sumalinog', 'Information technology', '2131', '09694242424', 'gaw123', '$2y$10$3e8IBlSr1xmJQYcZKRlVNuUJ0rVK5R7QtYBgQeIn8D.bABePRD46O', 'melody_lozada20@yahoo.com', 'teacher', 'verified'),
(29, 'meljohn', 'sumalinog', 'Information technology', '23232322', '09123111122', 'john23', '$2y$10$6hpObEgMAwRWuiMQ2loje.vbLniX8wc0nrKLuNzodEZ7IzlRMlFs2', 'melody_lozada2@yahoo.com', 'teacher', 'verified'),
(31, 'nadine', 'palermo', 'Information technology', '11111111111', '09123135313', 'nads12', '$2y$10$4OTQAZV/3tieyP2RrXgGNefTQPw1UtztdBjmPpjg5lxdBHmREYRl6', 'admin22222222@school.com', 'teacher', 'verified'),
(32, 'gerald', 'laspona', 'Information technology', '213122', '09695563215', 'john123', '$2y$10$ykXVexDs11MLeClbf/SbxuEjXntGzBOQGBj87eiQy7bZ99Aos1g1O', 'meljohnsumalinog1111111@gmail.com', 'teacher', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_import_history`
--
ALTER TABLE `data_import_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_assessments`
--
ALTER TABLE `student_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_assessment` (`student_id`,`assessment_type`,`assessment_date`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`attendance_date`),
  ADD KEY `idx_student_date` (`student_id`,`attendance_date`);

--
-- Indexes for table `student_participation`
--
ALTER TABLE `student_participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_participation` (`student_id`,`participation_date`);

--
-- Indexes for table `teacher_verification_history`
--
ALTER TABLE `teacher_verification_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gmail` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_import_history`
--
ALTER TABLE `data_import_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_assessments`
--
ALTER TABLE `student_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_participation`
--
ALTER TABLE `student_participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_verification_history`
--
ALTER TABLE `teacher_verification_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `teacher_verification_history`
--
ALTER TABLE `teacher_verification_history`
  ADD CONSTRAINT `teacher_verification_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_details` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
