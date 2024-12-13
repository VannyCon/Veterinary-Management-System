-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2024 at 02:49 PM
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
-- Database: `pet_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`) VALUES
(1, 'ervin', 'ervin123'),
(4, 'mm', 'mm123');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointment`
--

CREATE TABLE `tbl_appointment` (
  `id` int(10) NOT NULL,
  `appointment_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `service_id` varchar(50) NOT NULL,
  `isApproved` varchar(255) NOT NULL,
  `created_date` date NOT NULL,
  `created_time` time NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_appointment`
--

INSERT INTO `tbl_appointment` (`id`, `appointment_id`, `user_id`, `service_id`, `isApproved`, `created_date`, `created_time`) VALUES
(195, 'APT-A10CBE7556', 'USR_675a7cce84d0c', 'SERVICE-002', 'Approved', '2024-12-16', '14:06:17'),
(196, 'APT-422A264CF8', 'USR_675a80399349b', 'SERVICE-002', 'Approved', '2024-12-23', '14:21:40'),
(197, 'APT-5F18A6D0F1', 'USR_675a87318a760', 'SERVICE-004', 'Approved', '2024-12-31', '14:50:49'),
(198, 'APT-CE8F9D7A63', 'USR_675a7cce84d0c', 'SERVICE-003', 'Approved', '2024-12-17', '20:50:30'),
(199, 'APT-DD8A9E93DE', 'USR_675a87318a760', 'SERVICE-003', 'Declined', '2024-12-19', '21:00:32');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_appointment_pets`
--

CREATE TABLE `tbl_appointment_pets` (
  `id` int(10) NOT NULL,
  `appointment_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `pet_id` varchar(50) NOT NULL,
  `pet_symptoms` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_appointment_pets`
--

INSERT INTO `tbl_appointment_pets` (`id`, `appointment_id`, `user_id`, `pet_id`, `pet_symptoms`) VALUES
(176, 'APT-D48BA0A613', 'USR_675a561c5511f', 'PET_675a5b842ebe6', 'awdada'),
(177, 'APT-B1AF673222', 'USR_675a561c5511f', 'PET_675a59ab1af01', 'awdawdawd'),
(178, 'APT-F94B6375C9', 'USR_675a561c5511f', 'PET_675a5b842ebe6', 'awdada'),
(179, 'APT-D5181BAB04', 'USR_675a561c5511f', 'PET_675a61fd6c7d5', 'HAHAH'),
(180, 'APT-A10CBE7556', 'USR_675a7cce84d0c', 'PET_675a7d3fc94d0', 'vomiting\r\n'),
(181, 'APT-422A264CF8', 'USR_675a80399349b', 'PET_675a80b300ddc', 'not talking'),
(182, 'APT-5F18A6D0F1', 'USR_675a87318a760', 'PET_675a879db2f94', 'gapangagat'),
(183, 'APT-CE8F9D7A63', 'USR_675a7cce84d0c', 'PET_675a7d3fc94d0', 'sdadasdasdas'),
(184, 'APT-DD8A9E93DE', 'USR_675a87318a760', 'PET_675a879db2f94', 'ssssd vcbxvcbxcvb');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_calendar`
--

CREATE TABLE `tbl_calendar` (
  `id` int(10) NOT NULL,
  `date` date NOT NULL,
  `title` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_calendar`
--

INSERT INTO `tbl_calendar` (`id`, `date`, `title`) VALUES
(1, '2024-11-22', 'Heroes Day'),
(3, '2024-12-30', 'rizal day'),
(4, '2024-12-25', 'xmas');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_diagnosis`
--

CREATE TABLE `tbl_diagnosis` (
  `id` int(10) NOT NULL,
  `diagnosis_id` varchar(50) NOT NULL,
  `appointment_id` varchar(255) NOT NULL,
  `pet_id` varchar(50) NOT NULL,
  `pet_diagnosis` varchar(80) NOT NULL,
  `pet_medication_prescribe` varchar(100) NOT NULL,
  `pet_doctor_notes` varchar(100) NOT NULL,
  `isComplete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_diagnosis`
--

INSERT INTO `tbl_diagnosis` (`id`, `diagnosis_id`, `appointment_id`, `pet_id`, `pet_diagnosis`, `pet_medication_prescribe`, `pet_doctor_notes`, `isComplete`) VALUES
(53, 'DIAGNOSIS-B8AD018645', 'APT-A10CBE7556', 'PET_675a7d3fc94d0', 's', 's', 's', 1),
(54, 'DIAGNOSIS-B687D24A36', 'APT-422A264CF8', 'PET_675a80b300ddc', 'ulcer', 'biogesic', 'do not eat meat', 1),
(55, 'DIAGNOSIS-4404130E55', 'APT-5F18A6D0F1', 'PET_675a879db2f94', 'rabbies', 'vaccine', 'ok na', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pet`
--

CREATE TABLE `tbl_pet` (
  `id` int(10) NOT NULL,
  `pet_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `pet_name` varchar(50) NOT NULL,
  `pet_species` varchar(50) NOT NULL,
  `pet_age` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_pet`
--

INSERT INTO `tbl_pet` (`id`, `pet_id`, `user_id`, `pet_name`, `pet_species`, `pet_age`) VALUES
(67, 'PET_675a7d3fc94d0', 'USR_675a7cce84d0c', 'Lavander', 'Dog', '2 years old'),
(68, 'PET_675a80b300ddc', 'USR_675a80399349b', 'Coleen', 'Dog', '2 years old'),
(69, 'PET_675a82649eee4', 'USR_675a80399349b', 'Snowy Fall', 'Dog', '2 years old'),
(70, 'PET_675a8472117a0', 'USR_675a80399349b', 'Zada', 'Snake', '1 month'),
(71, 'PET_675a879db2f94', 'USR_675a87318a760', 'Snowy Fall', 'Dragon', '7');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service`
--

CREATE TABLE `tbl_service` (
  `id` int(10) NOT NULL,
  `service_id` varchar(50) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_service`
--

INSERT INTO `tbl_service` (`id`, `service_id`, `service_name`, `description`) VALUES
(1, 'SERVICE-001', 'Treatment', 'Treatment for the needs of your pets medication'),
(2, 'SERVICE-002', 'Dewormning', 'asdasdasdasdsad'),
(3, 'SERVICE-003', 'Iron Injection', 'lkkasjndjas'),
(4, 'SERVICE-004', 'Rabbies Vaccination', 'dmasdjkashd'),
(5, 'SERVICE-005', 'Castration', 'dmasdjkashd'),
(6, 'SERVICE-006', 'Vatamins and Mineral Supplementation', 'dmasdjkashd'),
(7, 'SERVICE-007', 'Artificaial Insemination', 'dmasdjkashd');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fullname` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`id`, `username`, `password`, `fullname`) VALUES
(29, 'john', 'zxc', 'michael');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(10) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` varchar(50) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `isApproved` tinyint(1) DEFAULT NULL,
  `process_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `user_id`, `username`, `password`, `fullname`, `address`, `phone_number`, `isApproved`, `process_by`) VALUES
(86, 'USR_675a7cce84d0c', 'john', 'zxc', 'john michael maningo', 'Brgy. Banquerohan Sitio Canipaan', '09663949324', 0, 'Admin'),
(87, 'USR_675a80399349b', 'Topee', 'Topee1017', 'Vin Lausa', 'Escalante', '09519709954', 1, 'Admin'),
(88, 'USR_675a85761df3d', 'erv', 'erv1', 'Ervin Ian Villaceran', 'SITIO CANIPAAN', '09663949324', 1, 'Staff'),
(89, 'USR_675a87318a760', 'joe', 'password123', 'Joe Basanta', 'sagay city', '09519709954', 0, 'Staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_appointment`
--
ALTER TABLE `tbl_appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `tbl_appointment_pets`
--
ALTER TABLE `tbl_appointment_pets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_calendar`
--
ALTER TABLE `tbl_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_diagnosis`
--
ALTER TABLE `tbl_diagnosis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_pet`
--
ALTER TABLE `tbl_pet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_service`
--
ALTER TABLE `tbl_service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_appointment`
--
ALTER TABLE `tbl_appointment`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `tbl_appointment_pets`
--
ALTER TABLE `tbl_appointment_pets`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `tbl_calendar`
--
ALTER TABLE `tbl_calendar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_diagnosis`
--
ALTER TABLE `tbl_diagnosis`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tbl_pet`
--
ALTER TABLE `tbl_pet`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_service`
--
ALTER TABLE `tbl_service`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
