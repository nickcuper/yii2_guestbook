-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 21, 2014 at 12:38 AM
-- Server version: 5.5.34-0ubuntu0.12.10.1
-- PHP Version: 5.4.6-1ubuntu1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;


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
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `country_id`, `name`) VALUES
(1, 1, 'Kievskaya'),
(2, 2, 'Poland'),
(3, 1, 'Dnepropetrovskaya'),
(4, 1, 'Kharkovskaya'),
(5, 1, 'Zaporojskaya'),
(6, 1, 'Poland1');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `password_hash` varchar(128) DEFAULT NULL,
  `auth_key` varchar(128) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fname` varchar(80) DEFAULT NULL,
  `lname` varchar(80) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `role_id` tinyint(2) unsigned NOT NULL DEFAULT '2',
  `state_id` tinyint(3) unsigned DEFAULT NULL,
  `country_id` int(10) DEFAULT '0',
  `avatar_url` varchar(80) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `wmr` varchar(20) DEFAULT NULL,
  `date_register` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `state_id` (`state_id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `login`, `password`, `password_hash`, `auth_key`, `email`, `fname`, `lname`, `is_active`, `role_id`, `state_id`, `country_id`, `avatar_url`, `address`, `phone`, `wmr`, `date_register`) VALUES
(2, 'admin', '', '$2y$13$CWHHaH9gQ6ys6doqSn0i1uQkkPp2azwVmo8Jgz6r9v7D86HJBTMZS', '587MURjSAl7i070ZJJgZ7T0Co91ojnx9', '55dmitrii@gmail.com', 'DM', 'PT', 1, 1, NULL, NULL, '537b964d88341.png', NULL, '', NULL, NULL),
(3, 'dmitrii', '', '$2y$13$CWHHaH9gQ6ys6doqSn0i1uQkkPp2azwVmo8Jgz6r9v7D86HJBTMZS', '587MURjSAl7i070ZJJgZ7T0Co91ojnx9', 'nick@multinet.dp.ua', 'dmitrii', 'dmirt', 0, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'dmitrii1', '', '$2y$13$v10B8TgIzzR0It7yJsWuAemE4czkFWsXwKT/F3TKhlrxWEnT7SGCS', 'LxUwQKe0rPrWJ2ANj22eojjlyuKzdX-C', 'nickl@mail.com', 'dmitrii', 'dmitrii', 1, 2, 2, 2, NULL, NULL, '123456', '123456', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;