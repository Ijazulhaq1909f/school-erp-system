-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2026 at 07:29 PM
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
-- Database: `school_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('Present','Absent','Late','Half Day') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `date`, `status`) VALUES
(3, 6, '2026-05-30', 'Absent'),
(4, 6, '2026-05-01', 'Present'),
(5, 6, '2026-05-03', 'Present'),
(6, 6, '2026-05-04', 'Present'),
(7, 6, '2026-05-05', 'Present'),
(8, 6, '2026-05-06', 'Present'),
(9, 6, '2026-05-07', 'Present'),
(10, 6, '2026-05-08', 'Present'),
(11, 6, '2026-05-10', 'Absent'),
(12, 6, '2026-05-11', 'Absent'),
(13, 7, '2026-05-30', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `capacity` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `section`, `teacher_id`, `room_no`, `capacity`) VALUES
(27, 'Nursery', 'A', NULL, 'N-101', 25),
(28, 'KGI', 'A', NULL, 'KG-201', 25),
(29, 'KGII', 'A', NULL, 'KG-301', 25),
(30, '1st', 'A', NULL, '101', 30),
(31, '2nd', 'A', NULL, '201', 30),
(32, '3rd', 'A', NULL, '301', 30),
(33, '4th', 'A', NULL, '401', 30),
(34, '5th', 'A', NULL, '501', 35),
(35, '6th', 'A', NULL, '601', 35),
(36, '7th', 'A', NULL, '701', 35),
(37, '8th', 'A', NULL, '801', 40),
(38, '9th', 'A', NULL, '901', 40),
(39, '10th', 'A', NULL, '1001', 40);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(100) DEFAULT NULL,
  `exam_type` enum('Mid-Term','Final-Term','Test','Monthly') DEFAULT 'Test',
  `class_name` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `max_marks` int(11) DEFAULT 75,
  `passing_marks` int(11) DEFAULT 33
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_name`, `exam_type`, `class_name`, `section`, `subject_id`, `exam_date`, `max_marks`, `passing_marks`) VALUES
(5, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 13, '2026-05-30', 75, 33),
(6, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 14, '2026-05-30', 75, 33),
(7, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 15, '2026-05-30', 75, 33),
(8, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 16, '2026-05-30', 60, 27),
(9, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 17, '2026-05-30', 60, 27),
(10, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 18, '2026-05-30', 60, 27),
(11, 'Mid-Term @026', 'Mid-Term', '10th', 'A', 19, '2026-05-30', 75, 33);

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('Paid','Pending','Partial','Overdue') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `amount`, `paid_amount`, `due_date`, `payment_date`, `payment_method`, `status`) VALUES
(2, 6, 5000.00, 5000.00, '2026-05-30', '2026-05-30', NULL, 'Paid'),
(3, 7, 3000.00, 2500.00, '2026-05-30', NULL, NULL, 'Partial');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` enum('urgent','event','general') DEFAULT 'general',
  `posted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `content`, `category`, `posted_by`, `created_at`) VALUES
(4, 'School Reopens', 'School Eid ul Azha ki tateelat k bad monday se open hoga...', 'general', 1, '2026-05-30 04:41:24');

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

CREATE TABLE `question_bank` (
  `id` int(11) NOT NULL,
  `class` varchar(20) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `chapter` varchar(100) DEFAULT NULL,
  `type` enum('MCQ','FillBlank','TrueFalse','Short','Long') DEFAULT NULL,
  `question` text DEFAULT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `marks` int(11) DEFAULT 1,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_bank`
--

INSERT INTO `question_bank` (`id`, `class`, `subject`, `chapter`, `type`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `answer`, `marks`, `uploaded_by`, `created_at`) VALUES
(1, '8th', 'Mathematics', 'Algebra', 'MCQ', 'x²+5x+6=0 ke roots hain?', '2,3', '-2,-3', '1,6', '-1,-6', '-2,-3', 1, NULL, '2026-05-30 04:02:11'),
(2, '8th', 'Mathematics', 'Algebra', 'MCQ', '2² = ?', '2', '4', '8', '16', '4', 1, NULL, '2026-05-30 04:02:11'),
(3, '8th', 'Mathematics', 'Algebra', 'Short', 'Solve: x²-7x+12=0', '', '', '', '', 'x=3,4', 5, NULL, '2026-05-30 04:02:11'),
(4, '8th', 'Mathematics', 'Algebra', 'Long', 'Solve using quadratic formula: 2x²-8x+6=0', '', '', '', '', 'x=1,3', 10, NULL, '2026-05-30 04:02:11');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `marks_obtained` decimal(5,2) DEFAULT NULL,
  `grade` varchar(5) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `exam_id`, `subject_id`, `marks_obtained`, `grade`, `remarks`) VALUES
(5, 6, 9, 17, 45.00, NULL, NULL),
(6, 6, 10, 18, 45.00, NULL, NULL),
(7, 6, 6, 14, 34.00, NULL, NULL),
(8, 6, 11, 19, 43.00, NULL, NULL),
(9, 6, 7, 15, 56.00, NULL, NULL),
(10, 6, 8, 16, 45.00, NULL, NULL),
(11, 6, 5, 13, 68.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `school_settings`
--

CREATE TABLE `school_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_settings`
--

INSERT INTO `school_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'school_name', 'Al-Samad English Grammar High School'),
(2, 'school_short', 'Al-Samad E.G.H School'),
(3, 'school_abbr', 'E.G.H.S'),
(4, 'school_address', 'Shanti Nagar, Ittehad Town, Baldia, Karachi'),
(5, 'school_phone', '03482343335'),
(6, 'school_whatsapp', '03453207748'),
(7, 'school_email', 'alsamadeghs@gmail.com'),
(8, 'principal_name', 'Ijaz ul Haq Khan'),
(9, 'school_motto', 'Education for Excellence'),
(10, 'current_session', '2026-2027');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `admission_no` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `class` varchar(20) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.png',
  `admission_date` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `admission_no`, `first_name`, `last_name`, `father_name`, `mother_name`, `dob`, `gender`, `class`, `section`, `phone`, `email`, `address`, `photo`, `admission_date`, `status`, `created_at`) VALUES
(6, 'ADM001', 'Ijaz', 'Khan', 'M Darwaish', 'aaa', '1994-02-03', 'Male', '10th', 'A', '03451234567', 'ijaz1909f@gmail.com', 'Ittihad Town', 'STD_1780131472.jpeg', '2026-05-30', 1, '2026-05-30 08:57:52'),
(7, 'ADM002', 'Dillo', 'Khan', 'Qasim', 'aaaaa', '2010-01-01', 'Female', 'KGII', 'A', '03451234567', 'ijaz1909f@gmail.com', 'Ittihad Town', 'STD_1780139806.png', '2026-05-30', 1, '2026-05-30 11:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `max_marks` int(11) DEFAULT 75,
  `passing_marks` int(11) DEFAULT 33,
  `subject_type` enum('Written','Oral','Practical') DEFAULT 'Written',
  `class_group` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `max_marks`, `passing_marks`, `subject_type`, `class_group`) VALUES
(1, 'Urdu', 75, 33, 'Written', '1-10'),
(2, 'English', 75, 33, 'Written', '1-10'),
(3, 'Mathematics', 75, 33, 'Written', '1-10'),
(13, 'Urdu/Sindhi', 75, 33, 'Written', '8-10'),
(14, 'English', 75, 33, 'Written', '8-10'),
(15, 'Mathematics', 75, 33, 'Written', '8-10'),
(16, 'Physics', 60, 27, 'Written', '8-10'),
(17, 'Biology', 60, 27, 'Written', '8-10'),
(18, 'Chemistry', 60, 27, 'Written', '8-10'),
(19, 'Islamiat/Pak.Studies', 75, 33, 'Written', '8-10'),
(20, 'Urdu', 75, 33, 'Written', '1-7'),
(21, 'English', 75, 33, 'Written', '1-7'),
(22, 'Mathematics', 75, 33, 'Written', '1-7'),
(23, 'Science', 75, 33, 'Written', '1-7'),
(24, 'Social Studies', 75, 33, 'Written', '1-7'),
(25, 'Computer', 75, 33, 'Written', '1-7'),
(26, 'Islamiat', 75, 33, 'Written', '1-7'),
(27, 'Drawing', 50, 25, 'Written', '1-7'),
(28, 'Urdu', 50, 25, 'Written', 'Nursery,KGI,KGII'),
(29, 'English', 50, 25, 'Written', 'Nursery,KGI,KGII'),
(30, 'Mathematics', 50, 25, 'Written', 'Nursery,KGI,KGII'),
(31, 'Islamiat', 25, 13, 'Written', 'Nursery,KGI,KGII'),
(32, 'Drawing', 25, 13, 'Practical', 'Nursery,KGI,KGII'),
(33, 'Urdu (Oral)', 50, 25, 'Oral', 'Nursery,KGI,KGII'),
(34, 'English (Oral)', 50, 25, 'Oral', 'Nursery,KGI,KGII'),
(35, 'Math (Oral)', 50, 25, 'Oral', 'Nursery,KGI,KGII');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `subject_specialty` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.png',
  `joining_date` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `employee_id`, `first_name`, `last_name`, `email`, `phone`, `qualification`, `subject_specialty`, `salary`, `address`, `photo`, `joining_date`, `status`, `created_at`) VALUES
(5, 'EMP001', 'Ijaz', 'Khan', 'ijaz1909f@gmail.com', '03482343335', 'M.Sc Mathematics', 'Mathematics', 30000.00, 'Ittihad Town', 'TCH_1780117808.jpeg', '2026-05-30', 1, '2026-05-30 05:10:08'),
(6, 'EMP002', 'Muhammad', 'Ahmad', 'ijaz1909f@gmail.com', '03451234567', 'B.A', 'English', 5000.00, 'Ittihad Town', 'TCH_1780140006.jpeg', '2026-05-30', 1, '2026-05-30 11:20:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','accountant','parent') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `related_id`, `status`, `last_login`, `created_at`) VALUES
(5, 'admin', '$2y$10$uYmHN9QWM9n6YnD0xH4LceGqOkxMUqGVcHNuc1ao6CAwmelZyWQkS', 'admin', NULL, 1, '2026-05-30 11:26:48', '2026-05-30 07:17:11'),
(6, 'teacher1', '$2y$10$vkJG9HULCZywLXj1bLKIoOcDa70PsuWKwUsiwM3BWxKZYyrdnmv6G', 'teacher', 1, 1, '2026-05-30 07:28:16', '2026-05-30 07:17:11'),
(7, 'parent1', '$2y$10$Jy1mtzdYc/fr0qkR.ru91.RILH7p5eKIFiLBryYvvwgsfGB/9SSHG', 'parent', 1, 1, '2026-05-30 11:25:56', '2026-05-30 07:17:11'),
(10, 'student1', '$2y$10$Lpgu/O8/Hv6mT4Obx7REmeaB37keprnP88g6ovelJ9mZFUfkNhDkK', 'parent', 1, 1, NULL, '2026-05-30 07:39:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_bank`
--
ALTER TABLE `question_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admission_no` (`admission_no`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

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
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `question_bank`
--
ALTER TABLE `question_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `school_settings`
--
ALTER TABLE `school_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
