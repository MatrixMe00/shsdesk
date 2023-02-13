-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2022 at 10:20 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shsdesk`
-- Database: `shsdesk2`
--
CREATE DATABASE IF NOT EXISTS shsdesk;
CREATE DATABASE IF NOT EXISTS shsdesk2;

-- --------------------------------------------------------

--
-- Table structure for table `admins_table`
--

CREATE TABLE `shsdesk`.`admins_table` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL DEFAULT 'New User',
  `email` varchar(80) NOT NULL,
  `password` varchar(80) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `contact` varchar(16) NOT NULL,
  `role` int(11) NOT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 1,
  `new_login` tinyint(1) NOT NULL DEFAULT 1,
  `adYear` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `admissiondetails`
--

CREATE TABLE `shsdesk`.`admissiondetails` (
  `schoolID` int(11) NOT NULL,
  `titleOfHead` varchar(50) NOT NULL DEFAULT 'Head Master',
  `headName` varchar(100) NOT NULL,
  `smsID` varchar(30) DEFAULT NULL,
  `admissionYear` year(4) NOT NULL,
  `academicYear` varchar(15) NOT NULL,
  `reopeningDate` varchar(40) DEFAULT NULL,
  `announcement` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cssps`
--

CREATE TABLE `shsdesk`.`cssps` (
  `indexNumber` varchar(20) NOT NULL,
  `Lastname` varchar(25) NOT NULL,
  `Othernames` varchar(60) NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `boardingStatus` enum('Day','Boarder') DEFAULT NULL,
  `programme` varchar(255) NOT NULL,
  `aggregate` int(11) NOT NULL,
  `jhsAttended` varchar(255) DEFAULT NULL,
  `dob` varchar(40) DEFAULT NULL,
  `trackID` varchar(20) NOT NULL,
  `schoolID` int(11) NOT NULL,
  `enroled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `enrol_table`
--

CREATE TABLE `shsdesk`.`enrol_table` (
  `indexNumber` varchar(20) NOT NULL,
  `enrolCode` varchar(15) NOT NULL,
  `shsID` int(11) NOT NULL,
  `aggregateScore` char(2) NOT NULL,
  `program` varchar(50) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `othername` varchar(60) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `jhsName` varchar(80) DEFAULT NULL,
  `jhsTown` varchar(60) DEFAULT NULL,
  `jhsDistrict` varchar(60) DEFAULT NULL,
  `birthdate` varchar(20) NOT NULL,
  `birthPlace` varchar(40) NOT NULL,
  `fatherName` varchar(100) DEFAULT NULL,
  `fatherOccupation` varchar(50) DEFAULT NULL,
  `motherName` varchar(100) DEFAULT NULL,
  `motherOccupation` varchar(50) DEFAULT NULL,
  `guardianName` varchar(100) DEFAULT NULL,
  `residentAddress` varchar(100) NOT NULL,
  `postalAddress` varchar(100) DEFAULT NULL,
  `primaryPhone` varchar(16) NOT NULL,
  `secondaryPhone` varchar(20) DEFAULT NULL,
  `interest` varchar(50) NOT NULL,
  `award` varchar(100) NOT NULL DEFAULT 'None',
  `position` varchar(70) NOT NULL DEFAULT 'None',
  `witnessName` varchar(80) NOT NULL,
  `witnessPhone` varchar(16) NOT NULL,
  `transactionID` varchar(30) DEFAULT NULL,
  `enrolDate` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `exeat`
--

CREATE TABLE `shsdesk`.`exeat` (
  `id` int(11) NOT NULL,
  `indexNumber` varchar(20) NOT NULL,
  `houseID` int(11) NOT NULL,
  `exeatTown` varchar(70) NOT NULL,
  `exeatDate` varchar(10) NOT NULL,
  `expectedReturn` varchar(10) NOT NULL,
  `returnDate` varchar(20) DEFAULT NULL,
  `exeatReason` varchar(80) NOT NULL,
  `exeatType` enum('Internal','External') NOT NULL,
  `school_id` int(11) NOT NULL,
  `givenBy` varchar(60) NOT NULL,
  `returnStatus` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `shsdesk`.`faq` (
  `id` int(11) NOT NULL,
  `Fullname` varchar(150) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `ContactNumber` varchar(16) NOT NULL,
  `Question` varchar(150) NOT NULL,
  `Answer` varchar(400) DEFAULT NULL,
  `Active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `houses`
--

CREATE TABLE `shsdesk`.`houses` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `schoolID` int(11) NOT NULL,
  `maleTotalRooms` int(11) DEFAULT NULL,
  `maleHeadPerRoom` int(11) DEFAULT NULL,
  `femaleTotalRooms` int(11) DEFAULT NULL,
  `femaleHeadPerRoom` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Both') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `house_allocation`
--

CREATE TABLE `shsdesk`.`house_allocation` (
  `indexNumber` varchar(20) NOT NULL,
  `schoolID` int(11) NOT NULL,
  `studentLname` varchar(20) NOT NULL,
  `studentOname` varchar(120) NOT NULL,
  `houseID` int(11) DEFAULT NULL,
  `studentYearLevel` int(11) NOT NULL DEFAULT 1,
  `studentGender` enum('Male','Female') NOT NULL,
  `boardingStatus` enum('Day','Boarder') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login_details`
--

CREATE TABLE `shsdesk`.`login_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` varchar(30) NOT NULL,
  `logout_time` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `shsdesk`.`notification` (
  `ID` int(11) NOT NULL,
  `Sender_id` int(11) NOT NULL,
  `Audience` varchar(255) NOT NULL,
  `School_id` int(11) DEFAULT NULL,
  `Notification_type` enum('notice','request','report') NOT NULL,
  `Title` varchar(60) NOT NULL,
  `Description` text NOT NULL,
  `Item_Read` tinyint(1) NOT NULL DEFAULT 0,
  `Read_by` text DEFAULT NULL,
  `Date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pageitemdisplays`
--

CREATE TABLE `shsdesk`.`pageitemdisplays` (
  `id` int(11) NOT NULL,
  `item_img` varchar(255) DEFAULT NULL,
  `image_alt` varchar(20) DEFAULT NULL,
  `item_page` varchar(20) NOT NULL,
  `item_type` varchar(20) NOT NULL,
  `item_head` varchar(40) DEFAULT NULL,
  `item_desc` text NOT NULL,
  `item_url` varchar(255) DEFAULT NULL,
  `item_button` tinyint(1) NOT NULL DEFAULT 0,
  `button_text` varchar(15) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `shsdesk`.`payment` (
  `id` int(11) NOT NULL,
  `transactionReference` varchar(20) DEFAULT NULL,
  `contactName` varchar(60) NOT NULL DEFAULT '-',
  `contactNumber` varchar(16) DEFAULT NULL,
  `user_role` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `method` enum('Mobile Money','Bank') DEFAULT NULL,
  `amount` decimal(5,2) NOT NULL,
  `deduction` decimal(5,2) NOT NULL DEFAULT 0.00,
  `studentNumber` int(11) NOT NULL,
  `date` varchar(30) DEFAULT NULL,
  `status` enum('Sent','Pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `shsdesk`.`reply` (
  `ID` int(11) NOT NULL,
  `Sender_id` int(11) NOT NULL,
  `Recipient_id` int(11) NOT NULL,
  `Comment_id` int(11) NOT NULL,
  `Message` varchar(400) NOT NULL,
  `AdminRead` tinyint(1) NOT NULL DEFAULT 0,
  `Read_by` text DEFAULT NULL,
  `Date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `shsdesk`.`roles` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 10,
  `access` tinyint(1) NOT NULL DEFAULT 1,
  `school_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `shsdesk`.`schools` (
  `id` int(11) NOT NULL,
  `logoPath` varchar(255) NOT NULL,
  `prospectusPath` varchar(255) NOT NULL,
  `admissionPath` text NOT NULL,
  `admissionHead` varchar(255) NOT NULL DEFAULT 'Offer Of Admission',
  `schoolName` varchar(150) NOT NULL,
  `postalAddress` varchar(40) NOT NULL,
  `abbr` varchar(50) DEFAULT NULL,
  `headName` varchar(150) NOT NULL,
  `techName` varchar(150) NOT NULL,
  `techContact` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` int(11) NOT NULL,
  `residence_status` enum('boarding','day','boarding/day') NOT NULL,
  `sector` enum('private','government') NOT NULL,
  `autoHousePlace` tinyint(1) NOT NULL DEFAULT 0,
  `Active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `school_category`
--

CREATE TABLE `shsdesk`.`school_category` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `shsdesk`.`transaction` (
  `transactionID` varchar(30) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `schoolBought` int(11) NOT NULL,
  `amountPaid` decimal(10,2) NOT NULL,
  `contactName` varchar(100) NOT NULL,
  `contactEmail` varchar(60) DEFAULT NULL,
  `Deduction` decimal(10,2) NOT NULL,
  `Transaction_Date` varchar(30) NOT NULL,
  `indexNumber` varchar(20) DEFAULT NULL,
  `Transaction_Expired` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `exeat`
--

CREATE TABLE `exeat` (
  `id` int(11) NOT NULL,
  `indexNumber` varchar(20) NOT NULL,
  `houseID` int(11) NOT NULL,
  `exeatTown` varchar(70) NOT NULL,
  `exeatDate` varchar(10) NOT NULL,
  `expectedReturn` varchar(10) NOT NULL,
  `returnDate` varchar(20) DEFAULT NULL,
  `exeatReason` varchar(80) NOT NULL,
  `exeatType` enum('Internal','External') NOT NULL,
  `school_id` int(11) NOT NULL,
  `givenBy` varchar(60) NOT NULL,
  `returnStatus` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `record_cleaning`
--

CREATE TABLE `record_cleaning` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `cleanDate` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students_table`
--

CREATE TABLE `students_table` (
  `indexNumber` varchar(25) NOT NULL,
  `Lastname` varchar(20) NOT NULL,
  `Othernames` varchar(40) NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `houseID` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `studentYear` int(11) NOT NULL,
  `guardianContact` varchar(20) NOT NULL,
  `programme` varchar(120) NOT NULL,
  `boardingStatus` enum('Boarder','Day') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins_table`
--
ALTER TABLE `shsdesk`.`admins_table`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role` (`role`),
  ADD KEY `school_id` (`school_id`) USING BTREE;

--
-- Indexes for table `admissiondetails`
--
ALTER TABLE `shsdesk`.`admissiondetails`
  ADD PRIMARY KEY (`schoolID`);

--
-- Indexes for table `cssps`
--
ALTER TABLE `shsdesk`.`cssps`
  ADD PRIMARY KEY (`indexNumber`),
  ADD KEY `schoolID` (`schoolID`);

--
-- Indexes for table `enrol_table`
--
ALTER TABLE `shsdesk`.`enrol_table`
  ADD PRIMARY KEY (`indexNumber`),
  ADD UNIQUE KEY `enrolCode` (`enrolCode`),
  ADD KEY `shsID` (`shsID`),
  ADD KEY `transactionID` (`transactionID`);

--
-- Indexes for table `exeat`
--
ALTER TABLE `shsdesk`.`exeat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `houseID` (`houseID`,`school_id`),
  ADD KEY `indexNumber` (`indexNumber`);

--
-- Indexes for table `faq`
--
ALTER TABLE `shsdesk`.`faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `houses`
--
ALTER TABLE `shsdesk`.`houses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schoolID` (`schoolID`);

--
-- Indexes for table `house_allocation`
--
ALTER TABLE `shsdesk`.`house_allocation`
  ADD PRIMARY KEY (`indexNumber`),
  ADD KEY `houseID` (`houseID`),
  ADD KEY `schoolID` (`schoolID`) USING BTREE;

--
-- Indexes for table `login_details`
--
ALTER TABLE `shsdesk`.`login_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `shsdesk`.`notification`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `School_id` (`School_id`);

--
-- Indexes for table `pageitemdisplays`
--
ALTER TABLE `shsdesk`.`pageitemdisplays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `shsdesk`.`payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_role` (`user_role`,`school_id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `shsdesk`.`reply`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Comment_id` (`Comment_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `shsdesk`.`roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `shsdesk`.`schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schoolName` (`schoolName`),
  ADD KEY `email` (`email`,`category`),
  ADD KEY `category` (`category`);

--
-- Indexes for table `school_category`
--
ALTER TABLE `shsdesk`.`school_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `shsdesk`.`transaction`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `schoolBought` (`schoolBought`),
  ADD KEY `indexNumber` (`indexNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins_table`
--
ALTER TABLE `shsdesk`.`admins_table`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exeat`
--
ALTER TABLE `shsdesk`.`exeat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `shsdesk`.`faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `houses`
--
ALTER TABLE `shsdesk`.`houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_details`
--
ALTER TABLE `shsdesk`.`login_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `shsdesk`.`notification`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pageitemdisplays`
--
ALTER TABLE `shsdesk`.`pageitemdisplays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `shsdesk`.`payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `shsdesk`.`reply`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `shsdesk`.`roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `shsdesk`.`schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school_category`
--
ALTER TABLE `shsdesk`.`school_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- Indexes for table `exeat`
--
ALTER TABLE `shsdesk2`.`exeat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `houseID` (`houseID`,`school_id`),
  ADD KEY `indexNumber` (`indexNumber`);

--
-- Indexes for table `record_cleaning`
--
ALTER TABLE `shsdesk2`.`record_cleaning`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_table`
--
ALTER TABLE `shsdesk2`.`students_table`
  ADD PRIMARY KEY (`indexNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exeat`
--
ALTER TABLE `shsdesk2`.`exeat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `record_cleaning`
--
ALTER TABLE `shsdesk2`.`record_cleaning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
