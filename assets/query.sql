CREATE TABLE `accesstable` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `indexNumber` varchar(16) NOT NULL,
 `accessToken` varchar(10) NOT NULL,
 `school_id` int(11) NOT NULL,
 `datePurchased` datetime NOT NULL,
 `expiryDate` datetime NOT NULL,
 `transactionID` varchar(20) NOT NULL,
 `status` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `announcement` (
 `id` int(11) NOT NULL,
 `school_id` int(11) NOT NULL,
 `heading` varchar(50) NOT NULL,
 `body` text NOT NULL,
 `audience` enum('teachers','students','all') NOT NULL,
 `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `courses` (
 `course_id` int(11) NOT NULL AUTO_INCREMENT,
 `school_id` int(11) NOT NULL,
 `course_name` varchar(150) NOT NULL,
 `short_form` varchar(50) DEFAULT NULL,
 `credit_hours` int(11) DEFAULT NULL,
 PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `exeat` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
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
 `returnStatus` tinyint(1) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 KEY `houseID` (`houseID`,`school_id`),
 KEY `indexNumber` (`indexNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `program` (
 `program_id` int(11) NOT NULL AUTO_INCREMENT,
 `school_id` int(11) NOT NULL,
 `program_name` varchar(255) NOT NULL,
 `short_form` varchar(30) DEFAULT NULL,
 `course_ids` varchar(255) NOT NULL,
 PRIMARY KEY (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `recordapproval` (
 `result_token` varchar(15) NOT NULL,
 `school_id` int(11) NOT NULL,
 `teacher_id` int(11) NOT NULL,
 `program_id` int(11) NOT NULL,
 `course_id` int(11) NOT NULL,
 `result_status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
 `submission_date` datetime NOT NULL,
 PRIMARY KEY (`result_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `record_cleaning` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `school_id` int(11) NOT NULL,
 `cleanDate` varchar(25) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `results` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `indexNumber` varchar(16) NOT NULL,
 `school_id` int(11) NOT NULL,
 `course_id` int(11) NOT NULL,
 `program_id` int(11) NOT NULL,
 `exam_type` enum('Test','Exam','Mock') NOT NULL,
 `class_mark` decimal(10,2) NOT NULL DEFAULT 0.00,
 `exam_mark` decimal(10,2) NOT NULL DEFAULT 0.00,
 `mark` decimal(10,2) NOT NULL,
 `result_token` varchar(15) NOT NULL,
 `teacher_id` int(11) NOT NULL,
 `exam_year` int(11) NOT NULL,
 `semester` int(11) NOT NULL,
 `accept_status` tinyint(1) NOT NULL DEFAULT 0,
 `date` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `students_table` (
 `indexNumber` varchar(25) NOT NULL,
 `password` varchar(100) NOT NULL DEFAULT '44ffe44097bbce02fbaa42734e92ae04',
 `Lastname` varchar(20) NOT NULL,
 `Email` varchar(100) DEFAULT NULL,
 `username` varchar(100) DEFAULT NULL,
 `Othernames` varchar(40) NOT NULL,
 `Gender` enum('Male','Female') NOT NULL,
 `houseID` int(11) NOT NULL,
 `school_id` int(11) NOT NULL,
 `studentYear` int(11) NOT NULL,
 `guardianContact` varchar(20) DEFAULT NULL,
 `programme` varchar(120) DEFAULT NULL,
 `program_id` int(11) DEFAULT NULL,
 `boardingStatus` enum('Boarder','Day') NOT NULL,
 PRIMARY KEY (`indexNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `teachers` (
 `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
 `lname` varchar(30) NOT NULL,
 `oname` varchar(60) DEFAULT NULL,
 `gender` enum('Male','Female') NOT NULL,
 `email` varchar(45) NOT NULL,
 `phone_number` varchar(16) NOT NULL,
 `school_id` int(11) NOT NULL,
 `status` tinyint(4) NOT NULL DEFAULT 1,
 `joinDate` datetime NOT NULL,
 PRIMARY KEY (`teacher_id`),
 UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `teacher_classes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `school_id` int(11) NOT NULL,
 `teacher_id` int(11) NOT NULL,
 `program_id` int(11) NOT NULL,
 `course_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `teacher_login` (
 `user_id` int(11) NOT NULL,
 `user_username` varchar(100) NOT NULL DEFAULT 'New User',
 `user_password` char(32) NOT NULL DEFAULT '44ffe44097bbce02fbaa42734e92ae04',
 PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `school_ussds` (
 `school_id` int(11) NOT NULL,
 `sms_id` varchar(11) NOT NULL,
 `status` enum('pending','reject','approve') NOT NULL DEFAULT 'pending',
 PRIMARY KEY (`school_id`),
 UNIQUE KEY `sms_id` (`sms_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transaction` (
 `transactionID` int(11) NOT NULL,
 `school_id` int(11) NOT NULL,
 `price` decimal(10,2) NOT NULL,
 `deduction` decimal(10,2) NOT NULL,
 `phoneNumber` char(10) NOT NULL,
 `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4