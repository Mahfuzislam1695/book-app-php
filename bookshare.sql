-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2019 at 06:57 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `AdminEmail` varchar(120) DEFAULT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `FullName`, `AdminEmail`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', 'bookshare@gmail.com', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '2019-07-20 18:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `book_tags`
--

CREATE TABLE `book_tags` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `book_tags`
--

INSERT INTO `book_tags` (`id`, `book_id`, `tag_id`) VALUES
(10, 9, 2),
(11, 9, 3),
(12, 10, 3),
(13, 10, 4),
(14, 11, 2),
(15, 11, 4),
(16, 12, 2),
(18, 14, 3),
(19, 14, 4),
(20, 14, 5),
(21, 15, 3),
(22, 15, 4),
(23, 15, 5),
(24, 16, 3),
(25, 16, 4),
(26, 16, 5),
(27, 17, 3),
(28, 17, 4),
(29, 17, 5),
(30, 18, 2),
(31, 18, 3),
(32, 18, 5),
(33, 19, 3),
(34, 19, 4),
(35, 19, 5),
(48, 13, 1),
(49, 13, 3),
(50, 20, 1),
(51, 20, 2),
(52, 20, 3),
(53, 21, 1),
(54, 21, 2),
(55, 21, 3),
(56, 22, 5),
(57, 23, 2),
(58, 24, 1),
(59, 24, 2),
(60, 25, 1),
(61, 25, 4),
(62, 26, 1),
(63, 26, 4),
(64, 26, 5);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `fromEmail` varchar(50) NOT NULL,
  `toEmail` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `book_ref` int(11) DEFAULT NULL,
  `added_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `fromEmail`, `toEmail`, `name`, `phone`, `message`, `status`, `book_ref`, `added_at`) VALUES
(35, 'nit@gmail.com', 'trin@gmail.com', 'Nit', '0156453423', 'I wanna rent this book.', 1, 25, '2019-07-19 08:23:55'),
(36, 'trin@gmail.com', 'nit@gmail.com', 'trin', '01564543423', 'I wanna exchange', 1, 26, '2019-07-19 08:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `send_to` varchar(100) NOT NULL,
  `book_ref` varchar(50) NOT NULL,
  `opened` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `send_to`, `book_ref`, `opened`) VALUES
(1, 'admin', '4567', 1),
(8, '1', '2147483647', 1),
(9, '2', '2147483647', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `fromEmail` varchar(50) NOT NULL,
  `toEmail` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `added_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`id`, `message_id`, `fromEmail`, `toEmail`, `name`, `phone`, `message`, `status`, `added_at`) VALUES
(171, 35, 'trin@gmail.com', 'nit@gmail.com', 'trin', '0156453423', 'Call me at 013452345', 1, '2019-07-19 08:24:31'),
(172, 36, 'nit@gmail.com', 'trin@gmail.com', 'nit', '01564543423', 'OK', 0, '2019-07-19 08:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(1, 'scifi'),
(2, 'business'),
(3, 'romance'),
(4, 'thiller'),
(5, 'etc');

-- --------------------------------------------------------

--
-- Table structure for table `tblauthors`
--

CREATE TABLE `tblauthors` (
  `id` int(11) NOT NULL,
  `AuthorName` varchar(159) DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `uploader_type` varchar(50) NOT NULL,
  `creationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblauthors`
--

INSERT INTO `tblauthors` (`id`, `AuthorName`, `uploaded_by`, `uploader_type`, `creationDate`, `UpdationDate`) VALUES
(16, 'Katherine Center', '1', 'user', '2019-07-19 18:12:22', NULL),
(17, 'J K Rowling', '1', 'user', '2019-07-19 18:13:17', NULL),
(18, 'W Dander', '1', 'user', '2019-07-19 18:13:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblbooks`
--

CREATE TABLE `tblbooks` (
  `id` int(11) NOT NULL,
  `BookName` varchar(255) DEFAULT NULL,
  `CatId` int(11) DEFAULT NULL,
  `AuthorId` int(11) DEFAULT NULL,
  `ISBNNumber` int(11) DEFAULT NULL,
  `BookPrice` int(11) DEFAULT NULL,
  `availability` tinyint(4) NOT NULL DEFAULT '1',
  `uploaded_by` varchar(50) NOT NULL,
  `uploaded_type` varchar(50) NOT NULL,
  `book_image` varchar(64) DEFAULT NULL,
  `book_description` text,
  `RegDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblbooks`
--

INSERT INTO `tblbooks` (`id`, `BookName`, `CatId`, `AuthorId`, `ISBNNumber`, `BookPrice`, `availability`, `uploaded_by`, `uploaded_type`, `book_image`, `book_description`, `RegDate`, `UpdationDate`) VALUES
(25, 'I Will Never Tell', 8, 16, 2147483647, 200, 1, '1', 'user', '5d320a2ce31593.25535077.png', 'From Catherine McKenzie, the instant bestselling author of The Good Liar, comes a riveting domestic suspense in the vein of Liane Moriarty that sees five siblings forced to confront a tragedy they thought was buried long ago.What happened to Amanda Holmes?', '2019-07-19 18:21:32', '2019-07-19 18:25:07'),
(26, 'Sherlock Holmes', 10, 18, 2147483647, 300, 1, '2', 'user', '5d320bfac05e85.90703899.jpg', 'The Adventures of Sherlock Holmes is a collection of twelve short stories by Arthur Conan Doyle, featuring his fictional detective Sherlock Holmes. It was first published on 14 October 1892; the individual stories had been serialised in The Strand Magazine between July 1891 and June 1892.', '2019-07-19 18:29:14', '2019-07-19 18:39:07');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `id` int(11) NOT NULL,
  `CategoryName` varchar(150) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`id`, `CategoryName`, `Status`, `CreationDate`, `UpdationDate`) VALUES
(8, 'Romantic', 1, '2019-07-19 18:14:28', '0000-00-00 00:00:00'),
(9, 'Technology', 1, '2019-07-19 18:14:50', '0000-00-00 00:00:00'),
(10, 'Science', 1, '2019-07-19 18:15:04', '0000-00-00 00:00:00'),
(11, 'Business', 1, '2019-07-19 18:15:15', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tblissuedbookdetails`
--

CREATE TABLE `tblissuedbookdetails` (
  `id` int(11) NOT NULL,
  `BookId` int(11) DEFAULT NULL,
  `StudentID` varchar(150) DEFAULT NULL,
  `StudentID_owner` varchar(50) DEFAULT NULL,
  `RetrunStatus` int(1) DEFAULT NULL,
  `fine` int(11) DEFAULT NULL,
  `uploader_type` varchar(50) DEFAULT NULL,
  `IssuesDate` datetime DEFAULT NULL,
  `ReturnDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblissuedbookdetails`
--

INSERT INTO `tblissuedbookdetails` (`id`, `BookId`, `StudentID`, `StudentID_owner`, `RetrunStatus`, `fine`, `uploader_type`, `IssuesDate`, `ReturnDate`) VALUES
(33, 25, '2', '1', 1, NULL, 'user', '2019-07-19 08:24:54', '2019-07-25 08:24:54'),
(34, 26, '1', '2', 1, NULL, 'user', '2019-07-19 08:32:28', '2019-07-31 08:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudents`
--

CREATE TABLE `tblstudents` (
  `id` int(11) NOT NULL,
  `StudentId` varchar(100) DEFAULT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `MobileNumber` char(11) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `Status` int(1) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblstudents`
--

INSERT INTO `tblstudents` (`id`, `StudentId`, `FullName`, `EmailId`, `MobileNumber`, `Password`, `Status`, `RegDate`, `UpdationDate`) VALUES
(13, '1', 'trin', 'trin@gmail.com', '0174534376', 'e10adc3949ba59abbe56e057f20f883e', 1, '2019-07-19 18:05:16', NULL),
(14, '2', 'nit', 'nit@gmail.com', '0176534352', 'e10adc3949ba59abbe56e057f20f883e', 1, '2019-07-19 18:09:41', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_tags`
--
ALTER TABLE `book_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblauthors`
--
ALTER TABLE `tblauthors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblstudents`
--
ALTER TABLE `tblstudents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `StudentId` (`StudentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `book_tags`
--
ALTER TABLE `book_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tblauthors`
--
ALTER TABLE `tblauthors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `tblissuedbookdetails`
--
ALTER TABLE `tblissuedbookdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `tblstudents`
--
ALTER TABLE `tblstudents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
