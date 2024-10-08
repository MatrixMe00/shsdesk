-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2022 at 11:43 AM
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

--
-- Dumping data for table `admins_table`
--

INSERT INTO `shsdesk`.`admins_table` (`user_id`, `fullname`, `username`, `email`, `password`, `school_id`, `contact`, `role`, `Active`, `new_login`, `adYear`) VALUES
(1, 'SHSDesk Developer', 'New User', 'developer@shsdesk.com', '21232f297a57a5a743894a0e4a801fc3', NULL, '233279284896', 1, 1, 1, '2022-01-10'),
(2, 'SHSDesk Superadmin', 'Superadmin', 'superadmin@shsdesk.com', '21232f297a57a5a743894a0e4a801fc3', NULL, '233278531456', 2, 1, 0, '2022-01-10'),
(3, 'John Doe', 'jonDoe', 'jdoe@example.com', '81dc9bdb52d04dc20036dbd8313ed055', 1, '233556129340', 3, 1, 0, '2022-01-11'),
(4, 'Oliver Kwao', 'Kwao', 'kwao@email.com', '81dc9bdb52d04dc20036dbd8313ed055', 1, '233547863214', 4, 1, 0, '2022-01-11'),
(5, 'Mario Graham', 'Mario', 'mario@email.com', '81dc9bdb52d04dc20036dbd8313ed055', 2, '233552367120', 3, 1, 0, '2022-01-12'),
(6, 'Donald Fin', 'Donald', 'donald@email.com', '81dc9bdb52d04dc20036dbd8313ed055', 2, '233579998214', 4, 1, 0, '2022-01-12');

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

--
-- Dumping data for table `admissiondetails`
--

INSERT INTO `shsdesk`.`admissiondetails` (`schoolID`, `titleOfHead`, `headName`, `smsID`, `admissionYear`, `academicYear`, `reopeningDate`, `announcement`) VALUES
(1, 'Head Master', 'Oliver Kwao', 'School One', 2022, '2021 / 2022', '2022-04-04', ''),
(2, 'Head Master', 'Donald Fin', 'School Two', 2022, '2021 / 2022', '2022-04-04', '');

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

--
-- Dumping data for table `cssps`
--

INSERT INTO `shsdesk`.`cssps` (`indexNumber`, `Lastname`, `Othernames`, `Gender`, `boardingStatus`, `programme`, `aggregate`, `jhsAttended`, `dob`, `trackID`, `schoolID`, `enroled`) VALUES
('010362005821', 'Osae', 'Rebecca Osaebea', 'Female', 'Boarder', 'General Textiles', 21, NULL, NULL, 'Single', 1, 0),
('010404501821', 'Dordoe', 'Bernice Adjo', 'Female', 'Boarder', 'Hospitality & Catering Management', 18, NULL, NULL, 'Single', 1, 0),
('010511100421', 'Buernatey', 'Joshua', 'Male', 'Day', 'Wood Construction Technology', 20, NULL, NULL, 'Single', 1, 0),
('010511701021', 'Teye', 'Lawrencia', 'Female', 'Day', 'General Textiles', 11, NULL, NULL, 'Single', 2, 0),
('010512002021', 'Tettey', 'Simon', 'Male', 'Boarder', 'Electronics Engineering', 17, NULL, NULL, 'Single', 2, 0),
('310202127021', 'Evans', 'Botsoe', 'Male', 'Boarder', 'Electrical Engineering Technology', 18, NULL, NULL, 'Single', 2, 0);

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

--
-- Dumping data for table `houses`
--

INSERT INTO `shsdesk`.`houses` (`id`, `title`, `schoolID`, `maleTotalRooms`, `maleHeadPerRoom`, `femaleTotalRooms`, `femaleHeadPerRoom`, `gender`) VALUES
(1, 'New One', 1, 8, 10, 6, 10, 'Both'),
(2, 'New Two', 1, 8, 15, 13, 10, 'Both');

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

--
-- Dumping data for table `pageitemdisplays`
--

INSERT INTO `shsdesk`.`pageitemdisplays` (`id`, `item_img`, `image_alt`, `item_page`, `item_type`, `item_head`, `item_desc`, `item_url`, `item_button`, `button_text`, `active`) VALUES
(1, 'assets/images/backgrounds/carousel/thought-catalog-xHaZ5BW9AY0-unsplash.jpg', 'Woman Writin', 'home', 'carousel', 'First Block', 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Corporis a dolore velit animi, placeat modi ea quaerat voluptas ullam reiciendis quibusdam laudantium adipisci fuga facilis repudiandae. Molestiae eius alias, enim autem obcaecati maiores ex dignissimos!', '', 1, 'Enter', 1),
(2, 'assets/images/backgrounds/carousel/joanna-kosinska-LAaSoL0LrYs-unsplash.jpg', 'Books and pencils', 'home', 'carousel', 'Second Block Item', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil iste quasi placeat dolorum earum unde tempora perspiciatis culpa ea a veniam neque repudiandae, quidem harum fugit blanditiis odio ipsa provident at libero aspernatur natus alias ratione pariatur. Corrupti, saepe dolore!', '', 0, '', 1),
(3, 'assets/images/backgrounds/carousel/joanna-kosinska-1_CMoFsPfso-unsplash.jpg', 'yellow board and pen', 'home', 'carousel', 'A Sleek Title', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam dolores explicabo laudantium excepturi veniam exercitationem natus ad dolorum eaque nobis labore ducimus fugit dolor ipsam dignissimos temporibus nostrum autem, sed, consectetur fugiat quaerat. Deserunt aliquam maiores repudiandae? Molestias vero placeat facilis explicabo harum quam, alias accusantium quaerat aperiam cum voluptatum animi velit quisquam officia maxime ullam, suscipit odio veritatis culpa.', '', 1, 'Read More', 1),
(4, 'assets/images/backgrounds/carousel/debby-hudson-kJO8GfbTx6w-unsplash_1.jpg', 'Pot on Books', 'home', 'carousel', 'Pot on Books Block', 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quia veritatis omnis esse. Vero doloremque atque corporis? Sed saepe eos tempore exercitationem quod odio nihil voluptatum dolore, aliquam ab. Illum quos magnam officiis fugiat!', '', 0, '', 1);

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

--
-- Dumping data for table `roles`
--

INSERT INTO `shsdesk`.`roles` (`id`, `title`, `price`, `access`, `school_id`) VALUES
(1, 'developer', 4, 1, 0),
(2, 'superadmin', 9, 1, 0),
(3, 'admin', 5, 1, 0),
(4, 'school head', 10, 1, 0),
(5, 'system', 2, 1, 0);

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

--
-- Dumping data for table `schools`
--

INSERT INTO `shsdesk`.`schools` (`id`, `logoPath`, `prospectusPath`, `admissionPath`, `schoolName`, `postalAddress`, `abbr`, `headName`, `techName`, `techContact`, `email`, `description`, `category`, `residence_status`, `sector`, `autoHousePlace`, `Active`) VALUES
(1, 'admin/admin/assets/images/schools/logo1.jpg', 'admin/admin/assets/files/default folder/prospectus.pdf', '&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!&lt;/p&gt;\\r\\n&lt;ul&gt;\\r\\n&lt;li&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!&lt;/li&gt;\\r\\n&lt;li&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!&lt;/li&gt;\\r\\n&lt;li&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!&lt;/li&gt;\\r\\n&lt;/ul&gt;\\r\\n&lt;p&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores sequi tempore ducimus assumenda, obcaecati, ut sunt laboriosam nobis temporibus exercitationem esse cum. Corrupti recusandae, modi necessitatibus tempora harum rerum repudiandae!&lt;/p&gt;', 'School Number One', 'P.O.Box Su 27, Suhum', 'SSTS', 'Oliver Kwao', 'John Doe', '233556129340', 'jdoe@example.com', '<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Fugit pariatur, eveniet tempora facilis molestiae id et? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloribus vel id nobis distinctio laboriosam laudantium eius, non, facilis accusantium commodi sit, modi quidem placeat.</p>', 1, 'boarding/day', 'government', 1, 1),
(2, 'admin/admin/assets/images/schools/logo2.jpg', 'admin/admin/assets/files/default files/prospectus.pdf', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate, autem. Voluptatibus, inventore. Odio quaerat distinctio soluta repellat necessitatibus dicta obcaecati, cumque delectus? Iusto facilis ipsa possimus suscipit dolor consectetur numquam modi nemo, fugit cupiditate accusantium sint illo dicta eius error laudantium id. Cumque maxime atque, quod non quisquam, quaerat tempora id dolorum distinctio qui aliquid nesciunt quo accusamus rem iusto quibusdam incidunt voluptate repellat odio, unde perferendis. Eum, sint vel dicta nostrum similique necessitatibus distinctio ut eaque dolorem architecto accusantium.', 'New School Two', 'P.O. Box KO 312, Koforidua', 'NST', 'Donald Fin', 'Mario Graham', '233552367120', 'mario@email.com', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. In delectus, libero nobis eligendi culpa accusantium aut velit deleniti voluptatibus esse tempore sit? Animi non quis beatae accusantium. Quia, voluptate dolorum.', 2, 'boarding/day', 'government', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `school_category`
--

CREATE TABLE `shsdesk`.`school_category` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `school_category`
--

INSERT INTO `shsdesk`.`school_category` (`id`, `title`) VALUES
(1, 'SHS'),
(3, 'SHT'),
(2, 'Technical');

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
-- Dumping data for table `transaction`
--

INSERT INTO `shsdesk`.`transaction` (`transactionID`, `contactNumber`, `schoolBought`, `amountPaid`, `contactName`, `contactEmail`, `Deduction`, `Transaction_Date`, `indexNumber`, `Transaction_Expired`) VALUES
('T12345678901234', '233255689641', 2, '30.00', 'Contact Name Demo', 'email@email.com', '0.59', '2022-04-02 14:52:03', NULL, 0),
('T125208218225648', '233279284896', 2, '30.00', 'SHSDesk', 'email@email.com', '0.59', '2022-03-29 06:32:09', NULL, 0),
('T148004111419515', '233279284896', 1, '30.00', 'SHSDesk', 'email@email.com', '0.59', '2022-03-29 07:43:54', NULL, 0),
('T167712297328658', '233554232123', 1, '30.00', 'SHSDesk', 'email@email.com', '0.59', '2022-04-03 15:58:09', NULL, 0);

--
-- Table structure for table `exeat`
--

CREATE TABLE `shsdesk2`.`exeat` (
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

CREATE TABLE `shsdesk2`.`record_cleaning` (
  `id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `cleanDate` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students_table`
--

CREATE TABLE `shsdesk2`.`students_table` (
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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `shsdesk`.`schools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `school_category`
--
ALTER TABLE `shsdesk`.`school_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

--
-- Indexes for table `exeat`
--
ALTER TABLE `exeat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `houseID` (`houseID`,`school_id`),
  ADD KEY `indexNumber` (`indexNumber`);

--
-- Indexes for table `record_cleaning`
--
ALTER TABLE `record_cleaning`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_table`
--
ALTER TABLE `students_table`
  ADD PRIMARY KEY (`indexNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exeat`
--
ALTER TABLE `exeat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `record_cleaning`
--
ALTER TABLE `record_cleaning`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
