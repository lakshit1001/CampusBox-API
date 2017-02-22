-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2017 at 11:30 AM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `example`
--

-- --------------------------------------------------------

--
-- Table structure for table `appriciateblog`
--

CREATE TABLE `appriciateblog` (
  `appriciate_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `appriciateblog`:
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int(11) NOT NULL,
  `programme_id` int(11) DEFAULT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `branches`:
--   `programme_id`
--       `programmes` -> `programme_id`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_group`
--

CREATE TABLE `class_group` (
  `class_group_id` int(10) NOT NULL,
  `branch_id` int(10) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `class_group`:
--

-- --------------------------------------------------------

--
-- Table structure for table `college`
--

CREATE TABLE `college` (
  `college_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `lat` float NOT NULL,
  `long` float NOT NULL,
  `address` text NOT NULL,
  `city` text NOT NULL,
  `logo` text NOT NULL,
  `cover_pic` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `college`:
--

--
-- Dumping data for table `college`
--

INSERT INTO `college` (`college_id`, `name`, `lat`, `long`, `address`, `city`, `logo`, `cover_pic`) VALUES
(1, 'Thapar', 33, 33, 'dws', 'Patiala', 'MyThapar.png', 'hahaha\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `collegeadmins`
--

CREATE TABLE `collegeadmins` (
  `id` int(11) NOT NULL,
  `student_id` int(10) NOT NULL,
  `college_id` int(11) NOT NULL,
  `rollnumber` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `collegeadmins`:
--   `college_id`
--       `college` -> `college_id`
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `collegeadmins`
--

INSERT INTO `collegeadmins` (`id`, `student_id`, `college_id`, `rollnumber`, `timestamp`) VALUES
(1, 1, 1, 0, '2017-02-16 17:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `phone` int(11) NOT NULL,
  `open` int(11) DEFAULT NULL,
  `type` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `contacts`:
--

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `content_id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL,
  `created_by_id` int(10) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content_type_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `content`:
--

-- --------------------------------------------------------

--
-- Table structure for table `content_reports`
--

CREATE TABLE `content_reports` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `content_id` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_id` int(10) NOT NULL,
  `reason` text NOT NULL,
  `reported` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `content_reports`:
--

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE `content_types` (
  `content_type_id` int(10) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `content_types`:
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL,
  `created_by_id` int(10) NOT NULL,
  `title` text NOT NULL,
  `subtitle` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `contactperson1` int(11) DEFAULT NULL,
  `contactperson2` int(11) DEFAULT NULL,
  `venue` text NOT NULL,
  `inter` tinyint(1) NOT NULL DEFAULT '0',
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_type_id` int(11) NOT NULL,
  `price` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `events`:
--   `college_id`
--       `college` -> `college_id`
--   `created_by_id`
--       `students` -> `student_id`
--   `event_type_id`
--       `event_types` -> `event_type_id`
--

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `college_id`, `created_by_id`, `title`, `subtitle`, `description`, `contactperson1`, `contactperson2`, `venue`, `inter`, `time_created`, `event_type_id`, `price`) VALUES
(1, 1, 1, 'event1', 33, 'event1 description', NULL, NULL, 'audi', 0, '2016-12-20 19:05:42', 1, 100),
(2, 1, 2, 'event 2', NULL, 'event 2 description', NULL, NULL, 'tan', 1, '2016-12-20 19:06:31', 2, 200);

-- --------------------------------------------------------

--
-- Table structure for table `event_bookmarks`
--

CREATE TABLE `event_bookmarks` (
  `event_bookmark__id` int(10) NOT NULL,
  `event_id` int(10) NOT NULL,
  `student_id` int(11) NOT NULL,
  `timed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_bookmarks`:
--   `event_id`
--       `events` -> `event_id`
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `event_bookmarks`
--

INSERT INTO `event_bookmarks` (`event_bookmark__id`, `event_id`, `student_id`, `timed`) VALUES
(1, 2, 1, '2017-02-22 05:20:20'),
(2, 1, 1, '2017-02-22 05:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `event_likes`
--

CREATE TABLE `event_likes` (
  `likes_id` int(10) NOT NULL,
  `event_id` int(10) NOT NULL,
  `student_id` int(11) NOT NULL,
  `timed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

--
-- RELATIONS FOR TABLE `event_likes`:
--

-- --------------------------------------------------------

--
-- Table structure for table `event_reports`
--

CREATE TABLE `event_reports` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `event_id` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type_id` int(10) NOT NULL,
  `reason` text NOT NULL,
  `reported` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_reports`:
--

-- --------------------------------------------------------

--
-- Table structure for table `event_tags`
--

CREATE TABLE `event_tags` (
  `event_tag_id` int(10) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_tags`:
--

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `event_type_id` int(10) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_types`:
--

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`event_type_id`, `name`) VALUES
(1, 'Technical'),
(2, 'Cultural');

-- --------------------------------------------------------

--
-- Table structure for table `event_updates`
--

CREATE TABLE `event_updates` (
  `event_update_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `title` text,
  `message` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `color` text,
  `society_id` int(11) DEFAULT NULL,
  `student_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_updates`:
--   `event_id`
--       `events` -> `event_id`
--   `society_id`
--       `societies` -> `id`
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `event_updates`
--

INSERT INTO `event_updates` (`event_update_id`, `event_id`, `title`, `message`, `timestamp`, `color`, `society_id`, `student_id`) VALUES
(1, 7, 'New Blog.', 'it has be', '2016-10-16 18:24:13', 'alert-warning', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_views`
--

CREATE TABLE `event_views` (
  `event_view_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `device_id` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `event_views`:
--

--
-- Dumping data for table `event_views`
--

INSERT INTO `event_views` (`event_view_id`, `student_id`, `event_id`, `timestamp`, `device_id`) VALUES
(1, 0, 1, '0000-00-00 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(10) NOT NULL,
  `following_id` int(10) NOT NULL,
  `follower_id` int(10) NOT NULL,
  `timestamp` timestamp(1) NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- RELATIONS FOR TABLE `followers`:
--

-- --------------------------------------------------------

--
-- Table structure for table `hostel`
--

CREATE TABLE `hostel` (
  `hostel_id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `gender` text NOT NULL,
  `mess` tinyint(1) NOT NULL DEFAULT '1',
  `lat` float NOT NULL,
  `long` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `hostel`:
--

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `synonyms` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `interests`:
--

-- --------------------------------------------------------

--
-- Table structure for table `lastviewed`
--

CREATE TABLE `lastviewed` (
  `user_id` int(11) NOT NULL,
  `events` timestamp NULL DEFAULT NULL,
  `projects` timestamp NULL DEFAULT NULL,
  `store` timestamp NULL DEFAULT NULL,
  `notes` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `noticeboard` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `lastviewed`:
--

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `device` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `logins`:
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `api` text,
  `requestType` text NOT NULL,
  `deviceType` text NOT NULL,
  `deviceOs` text NOT NULL,
  `ipAddress` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `logs`:
--

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `username`, `api`, `requestType`, `deviceType`, `deviceOs`, `ipAddress`) VALUES
(4, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99'),
(3, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99'),
(5, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99'),
(6, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99'),
(7, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99'),
(8, 'rohanrohan', '/event/:id', 'get', 'mobile', 'ios', '14.141.244.99');

-- --------------------------------------------------------

--
-- Table structure for table `noticeboard`
--

CREATE TABLE `noticeboard` (
  `id` int(11) NOT NULL,
  `college` int(11) NOT NULL,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `userId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `destroyOn` datetime NOT NULL,
  `year` int(11) NOT NULL,
  `branchId` int(11) NOT NULL,
  `status` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `noticeboard`:
--

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `participant_id` int(10) NOT NULL,
  `event_id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `participants`:
--   `event_id`
--       `events` -> `event_id`
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`participant_id`, `event_id`, `student_id`) VALUES
(1, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `programme_id` int(11) NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `programmes`:
--   `college_id`
--       `college` -> `college_id`
--

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`programme_id`, `college_id`, `name`) VALUES
(1, 1, 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `request_contact`
--

CREATE TABLE `request_contact` (
  `id` int(10) NOT NULL,
  `req_by_id` int(11) NOT NULL,
  `req_to_id` int(11) NOT NULL,
  `req_field` int(11) NOT NULL,
  `status` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `request_contact`:
--

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(10) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `skills`:
--

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `name`) VALUES
(1, 'AngularJs'),
(2, 'Espanol'),
(3, 'nodejs'),
(4, 'skateboard'),
(5, 'debating'),
(6, 'painting');

-- --------------------------------------------------------

--
-- Table structure for table `social_ids`
--

CREATE TABLE `social_ids` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `facebook` text NOT NULL,
  `instagram` text NOT NULL,
  `github` text NOT NULL,
  `behance` text NOT NULL,
  `soundcloud` text NOT NULL,
  `linkedin` text NOT NULL,
  `other` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `social_ids`:
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `social_ids`
--

INSERT INTO `social_ids` (`id`, `student_id`, `facebook`, `instagram`, `github`, `behance`, `soundcloud`, `linkedin`, `other`) VALUES
(1, 1, 'facebook.com/test', 'instagram.com/test', 'github.com/test', 'behance.com/test', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `societies`
--

CREATE TABLE `societies` (
  `id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `dp` text NOT NULL,
  `created_by` int(10) NOT NULL,
  `website` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `societies`:
--

-- --------------------------------------------------------

--
-- Table structure for table `society_skills`
--

CREATE TABLE `society_skills` (
  `id` int(10) NOT NULL,
  `society_id` int(10) NOT NULL,
  `skill_id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `society_skills`:
--

--
-- Dumping data for table `society_skills`
--

INSERT INTO `society_skills` (`id`, `society_id`, `skill_id`, `college_id`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `studentinterests`
--

CREATE TABLE `studentinterests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `studentinterests`:
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(10) NOT NULL,
  `class_group_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `username` text,
  `roll_number` int(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` int(10) DEFAULT NULL,
  `photo` text,
  `hostel_id` int(11) DEFAULT NULL,
  `room_number` text,
  `home_city` text,
  `grad_id` int(10) DEFAULT NULL,
  `branch_id` int(10) DEFAULT NULL,
  `year` text,
  `class_id` int(10) DEFAULT NULL,
  `passout_year` int(10) DEFAULT NULL,
  `age` int(10) DEFAULT NULL,
  `gender` text,
  `college_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `students`:
--

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `class_group_id`, `name`, `username`, `roll_number`, `email`, `phone`, `photo`, `hostel_id`, `room_number`, `home_city`, `grad_id`, `branch_id`, `year`, `class_id`, `passout_year`, `age`, `gender`, `college_id`) VALUES
(1, 1, 'Lakshit Anand', 'lakshit1001', 101506031, 'lakshit1001@ymail.com', 594664, NULL, NULL, 'B305', 'Delhi', NULL, NULL, '2018', NULL, 2017, NULL, 'Male', 1),
(2, 2, 'Aditya Chawla', 'chawlaaditya8', 21443211, 'chawlaaditya8@gmail.com', 8860, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_skills`
--

CREATE TABLE `student_skills` (
  `id` int(10) NOT NULL,
  `student_id` int(10) NOT NULL,
  `skill_name` text NOT NULL,
  `proficiency` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `student_skills`:
--   `student_id`
--       `students` -> `student_id`
--

--
-- Dumping data for table `student_skills`
--

INSERT INTO `student_skills` (`id`, `student_id`, `skill_name`, `proficiency`) VALUES
(1, 1, 'AngularJS', 70),
(2, 1, 'Espanol', 30),
(3, 2, 'Skateboard', 10);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `collegeId` int(11) NOT NULL,
  `programId` int(11) NOT NULL,
  `code` text NOT NULL,
  `name` text NOT NULL,
  `credits` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `subjects`:
--

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(10) NOT NULL,
  `college_id` int(10) NOT NULL,
  `society_id` int(10) NOT NULL,
  `societyUsername` text,
  `added_by_id` int(10) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` int(10) NOT NULL,
  `position` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `team_members`:
--

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `college_id`, `society_id`, `societyUsername`, `added_by_id`, `name`, `email`, `phone`, `position`) VALUES
(1, 1, 1, 'anubhooti', 2, 'sahil', 'sahil.nagi@gmail.com', 2147483647, 'Gensec');

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `uid` text NOT NULL,
  `title` text NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- RELATIONS FOR TABLE `todos`:
--

--
-- Dumping data for table `todos`
--

INSERT INTO `todos` (`id`, `order`, `uid`, `title`, `completed`, `created_at`, `updated_at`) VALUES
(0, 10, '88IRgBqjGNc9', 'Test the API', 0, '2017-02-11 02:12:55', '2017-02-11 02:12:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appriciateblog`
--
ALTER TABLE `appriciateblog`
  ADD PRIMARY KEY (`appriciate_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `class_group`
--
ALTER TABLE `class_group`
  ADD PRIMARY KEY (`class_group_id`);

--
-- Indexes for table `college`
--
ALTER TABLE `college`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `collegeadmins`
--
ALTER TABLE `collegeadmins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`content_id`);

--
-- Indexes for table `content_reports`
--
ALTER TABLE `content_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content_types`
--
ALTER TABLE `content_types`
  ADD PRIMARY KEY (`content_type_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_bookmarks`
--
ALTER TABLE `event_bookmarks`
  ADD PRIMARY KEY (`event_bookmark__id`);

--
-- Indexes for table `event_likes`
--
ALTER TABLE `event_likes`
  ADD PRIMARY KEY (`likes_id`);

--
-- Indexes for table `event_types`
--
ALTER TABLE `event_types`
  ADD PRIMARY KEY (`event_type_id`);

--
-- Indexes for table `event_updates`
--
ALTER TABLE `event_updates`
  ADD PRIMARY KEY (`event_update_id`);

--
-- Indexes for table `event_views`
--
ALTER TABLE `event_views`
  ADD PRIMARY KEY (`event_view_id`);

--
-- Indexes for table `hostel`
--
ALTER TABLE `hostel`
  ADD PRIMARY KEY (`hostel_id`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lastviewed`
--
ALTER TABLE `lastviewed`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`participant_id`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`programme_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`);

--
-- Indexes for table `societies`
--
ALTER TABLE `societies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studentinterests`
--
ALTER TABLE `studentinterests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `id` (`student_id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appriciateblog`
--
ALTER TABLE `appriciateblog`
  MODIFY `appriciate_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `collegeadmins`
--
ALTER TABLE `collegeadmins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `content_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `content_types`
--
ALTER TABLE `content_types`
  MODIFY `content_type_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `event_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
