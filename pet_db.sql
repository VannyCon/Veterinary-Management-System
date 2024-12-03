-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 01:36 PM
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
(1, 'ervin', 'ervin123');

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
  `created_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_appointment`
--

INSERT INTO `tbl_appointment` (`id`, `appointment_id`, `user_id`, `service_id`, `isApproved`, `created_date`, `created_time`) VALUES
(1, 'APT-001', 'USER-001', 'SERVICE-001', 'Approved', '2024-11-23', '10:30:00'),
(3, 'APT-003', 'USER-001', 'SERVICE-002', 'Decline', '2024-11-25', '13:10:00'),
(4, 'APT-004', 'USER-001', 'SERVICE-001', 'Denied', '2024-11-23', '13:15:00'),
(5, 'APT-005', 'USER-005', 'SERVICE-004', 'Pending', '2024-11-24', '13:20:00'),
(6, 'APT-006', 'USER-006', 'SERVICE-005', 'Pending', '2024-11-24', '13:25:00'),
(7, 'APT-007', 'USER-007', 'SERVICE-001', 'Pending', '2024-11-25', '13:30:00'),
(8, 'APT-008', 'USER-008', 'SERVICE-002', 'Pending', '2024-11-25', '13:35:00'),
(9, 'APT-009', 'USER-009', 'SERVICE-003', 'Pending', '2024-11-26', '13:40:00'),
(10, 'APT-010', 'USER-010', 'SERVICE-004', 'Pending', '2024-11-27', '13:45:00'),
(11, 'APT-011', 'USER-011', 'SERVICE-005', 'Pending', '2024-11-28', '13:50:00'),
(12, 'APT-012', 'USER-012', 'SERVICE-001', 'Pending', '2024-11-29', '13:55:00'),
(14, 'APT-7A4AAD708F', 'USER-001', 'Deworming', 'Pending', '2024-11-11', '05:42:00'),
(15, 'APT-EDBEE15226', 'USER-001', 'SERVICE-002', 'Approved', '2024-11-01', '05:47:00'),
(16, 'APT-4EBC2DDB81', 'USER-001', 'SERVICE-001', 'Denied', '2024-11-02', '05:48:00'),
(21, 'APT-C7059505C2', 'USER-001', 'SERVICE-001', 'Approved', '2024-11-21', '05:54:00'),
(22, 'APT-C012C22424', 'USER-001', 'SERVICE-001', '0', '2024-11-15', '08:20:00'),
(23, 'APT-3AEEB7D8B3', 'USER-001', 'SERVICE-001', 'Approved', '2024-11-05', '08:22:00'),
(24, 'APT-6940CC64C6', 'USER-001', 'SERVICE-002', 'Approved', '2024-11-03', '08:29:00'),
(25, 'APT-C08AA503E5', 'USER-001', 'SERVICE-001', 'Approved', '2024-11-20', '08:36:00');

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
(1, 'APT-001', 'USER-001', 'PET-1001', 'Sakit tyan'),
(2, 'APT-003', 'USER-001', 'PET-1001', 'Skin Issues'),
(3, 'APT-004', 'USER-001', 'PET-1002', 'ga lingin ang tiyan\r\n'),
(4, 'USER-001', 'PET-001', 'Deworming', 'asdasdsa asdasdasdasd'),
(5, 'USER-001', 'PET-002', 'SERVICE-002', 'ga ubo ubo, ga suka'),
(6, 'USER-001', 'PET-001', 'SERVICE-001', 'sadsadsadasd sadasdsad asdasdasd'),
(7, 'APT-C7059505C2', 'USER-001', 'PET-1001', 'zdsa'),
(8, 'APT-C012C22424', 'USER-001', 'PET-1001', 'ga lain ang utok'),
(9, 'APT-3AEEB7D8B3', 'USER-001', 'PET-1001', 'biskan lng ahhh'),
(10, 'APT-6940CC64C6', 'USER-001', 'PET-1002', 'vomiting'),
(11, 'APT-C08AA503E5', 'USER-001', 'PET-1001', 'headache');

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
(1, '2024-11-22', 'Heroes Day');

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
(8, 'DIAGNOSIS-C17D5F4142', 'APT-001', 'PET-1001', 'sss', 'sss', 'sss', 1),
(9, 'DIAGNOSIS-7A82E131FA', 'APT-6940CC64C6', 'PET-1002', 'sdasdsadsad', 'asdasdasd', 'asdasdasdasdasdsa asdasdas', 1),
(10, 'DIAGNOSIS-5FDE19EB5B', 'APT-C08AA503E5', 'PET-1001', 'sadljkfjasdfasdflj;adsf;l ', 'asdasdasdas ', 'asdasdsadsadasd', 1),
(11, 'DIAGNOSIS-670D1A3021', 'APT-3AEEB7D8B3', 'PET-1001', 'asdasd', 'asdsad', 'asdasds', 1);

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
(1, 'PET-1001', 'USER-001', 'Kitty', 'Cat', '2 Years Old'),
(2, 'PET-1002', 'USER-001', 'Bogarts', 'Dog', '1'),
(3, 'PET_674d7b8a79b79', 'USR_674aad7a5138a', 'Leonard', 'Cat', '3 months');

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
(2, 'SERVICE-002', 'Dewormning', 'asdasdasdasdsad');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`id`, `username`, `password`) VALUES
(1, 'mherjoy', 'mherjoy123'),
(3, 'mikel', 'mikel123');

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
  `isApproved` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `user_id`, `username`, `password`, `fullname`, `address`, `phone_number`, `isApproved`) VALUES
(2, 'USR_674aad7a5138a', 'ervin', 'ervin123', 'Ervin Ian Villaceran', 'Brgy Zone 5, Cadiz City, Negros Occidental', '09663949324', 1),
(5, 'USR_674ac97078699', 'awdawd', 'awdaw', 'awdaw', 'dawdaw', 'dawd', 1),
(9, 'USR_674acdcfe2af7', 'AWDAW', 'AWDAW', 'AWDA', 'WDAWDA', 'DAWDAWD', 1),
(12, 'USR_674ad47780431', 'mikel', 'undefined', 'Mikel Maningo', 'Zone 1', '09663949324', 1),
(13, 'USR_674ad8791ee15', 'kyla', 'kyla123', 'Mherjoy Bedayos', 'SITIO CANIPAAN', '09663949324', 1),
(14, 'USR_674ad8a4512a4', 'awdaw', 'awdawd', 'awd', 'awdawd', 'awdawd', 1),
(15, 'USR_674d5f377b7a2', 'pending', 'pending', 'sss', 'ss', 's', 1),
(16, 'USR_674d60efb9b15', 'xx', 'xx', 'xx', 'xx', 'xx', 1),
(18, 'USR_674d61e6e796f', 'floryJOhnManingo', 'floryJOhnManingo', 'floryJOhnManingo', 'floryJOhnManingo', 'floryJOhnManingo', 1),
(19, 'USR_674ef8f1793cf', 'newuser', 'newuser', 'new user', 'newuser', 'newuser', 1);

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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_appointment`
--
ALTER TABLE `tbl_appointment`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_appointment_pets`
--
ALTER TABLE `tbl_appointment_pets`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_calendar`
--
ALTER TABLE `tbl_calendar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_diagnosis`
--
ALTER TABLE `tbl_diagnosis`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_pet`
--
ALTER TABLE `tbl_pet`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_service`
--
ALTER TABLE `tbl_service`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
