-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2015 at 05:59 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `land`
--

-- --------------------------------------------------------

--
-- Table structure for table `land`
--

CREATE TABLE IF NOT EXISTS `land` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coord_key` varchar(8) NOT NULL,
  `user_key` int(10) unsigned NOT NULL,
  `land_name` varchar(512) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `primary_color` varchar(10) NOT NULL,
  `secondary_color` varchar(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `land`
--

INSERT INTO `land` (`id`, `coord_key`, `user_key`, `land_name`, `content`, `primary_color`, `secondary_color`, `created`, `modified`) VALUES
(1, '26|-80', 0, 'Alexville', 'This is my land!', '', '', '2015-12-24 07:02:15', '2015-12-24 16:31:39');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `password` varchar(256) NOT NULL,
  `facebook_id` int(16) NOT NULL,
  `email` varchar(256) NOT NULL,
  `desc` text NOT NULL,
  `location` varchar(256) NOT NULL,
  `primary_color` varchar(10) NOT NULL,
  `secondary_color` varchar(10) NOT NULL,
  `flag_image` varchar(512) NOT NULL,
  `profile_image` varchar(512) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `facebook_id`, `email`, `desc`, `location`, `primary_color`, `secondary_color`, `flag_image`, `profile_image`, `created`, `modified`) VALUES
(1, 'goose', '$2y$10$VvLWbplOf4RQIL4mMMIiNuwsSajn6tvXhCdfdBizvmgi3c4hsLQXC', 0, 'placeholder@gmail.com', '', '', '', '', '', '', '2015-12-24 05:24:37', '2015-12-24 05:24:37'),
(2, 'alex', '$2y$10$5iI.wsEemKnWJmWW0nQdku2GYC3fbHid/f0LgB5FP7Y34ap.opl/C', 0, 'placeholder@gmail.com', '', '', '', '', '', '', '2015-12-24 15:09:09', '2015-12-24 15:09:09'),
(3, 'bob', '$2y$10$44SV3dQEtd4/Qu2.3cOCdewAUYPDYhzr1JEIhHJZbltvYadm03Ld.', 0, 'placeholder@gmail.com', '', '', '', '', '', '', '2015-12-24 15:13:02', '2015-12-24 15:13:02');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
