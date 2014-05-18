--
-- Database: `guestbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from` int(10) unsigned NOT NULL,
  `parent_id` int(11) NOT NULL,
  `body` varchar(255) DEFAULT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `from`, `parent_id`, `body`, `date_create`) VALUES
(1, 5, 0, 'asdasdasdasd', '2014-05-18 12:29:19'),
(2, 5, 0, 'asdasddas1', '2014-05-18 19:24:53'),
(3, 5, 1, 'asdasddas1', '2014-05-18 21:20:00'),
(4, 5, 1, 'asdasddas1', '2014-05-18 21:20:00'),
(5, 12, 0, 'asdasdasdasd', '2014-05-18 12:29:19'),
(9, 12, 5, 'jhh', '2014-05-18 22:12:02'),
(10, 12, 5, 'khkh', '2014-05-18 22:12:46'),
(11, 12, 5, 'khkh', '2014-05-18 22:17:58'),
(12, 12, 5, 'jhnkjhn', '2014-05-18 22:18:19'),
(13, 12, 5, '5646', '2014-05-18 22:19:49'),
(14, 12, 5, '8952', '2014-05-18 22:20:11'),
(15, 12, 0, 'sadasd', '2014-05-18 22:20:11'),
(16, 12, 15, '45555', '2014-05-18 22:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `name`) VALUES
(1, 'Ukraine'),
(2, 'Poland');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` tinyint(3) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`state_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `country_id`, `name`) VALUES
(1, 1, 'Kievksaya'),
(2, 2, 'Mazowieckie'),
(3, 1, 'Donetckaya'),
(4, 2, 'Dolnośląskie'),
(5, 2, 'Lubelskie');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `password_hash` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fname` varchar(80) DEFAULT NULL,
  `lname` varchar(80) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `role_id` tinyint(2) unsigned NOT NULL DEFAULT '2',
  `state_id` tinyint(3) unsigned DEFAULT NULL,
  `country_id` tinyint(3) DEFAULT NULL,
  `auth_key` varchar(128) DEFAULT NULL,
  `wmr` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(128) DEFAULT NULL,
  `date_register` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `login`, `password`, `password_hash`, `email`, `fname`, `lname`, `is_active`, `role_id`, `state_id`, `country_id`, `auth_key`, `wmr`, `phone`, `avatar_url`, `date_register`) VALUES
(5, 'admin', '', '$2y$13$BcE3.ShAg2XwJXDjMSsbV..3eq5cyxf.SiPPU1XlSvuGMjBfsRTAW', '55dmitrii@gmail.com', 'adminchik', 'admin', 1, 2, NULL, 0, 'Kgo5GWkcbIos1MempFWV8gxM_JJLBACb', NULL, '02369', '53789ed3ced86.jpg', NULL),
(12, 'admin1', '', '$2y$13$Jw8X28DQdMYjVOSW.dMDIOwnR9XHfuIBcZxoTvPoGCF8Q2D/Ux7j2', '5512dmitrii@gmail.com', 'sadas', 'sadasd', 1, 2, 3, 1, 'Mh9HkdOfJ-qAv0xXgN5o8UbjtrTZ-lQM', '541646456', '654646', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;