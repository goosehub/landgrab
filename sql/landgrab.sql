-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2016 at 02:45 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `landgrab`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_key` int(10) unsigned NOT NULL,
  `world_key` int(10) unsigned NOT NULL,
  `active_account` int(1) unsigned NOT NULL,
  `active_army` int(10) NOT NULL,
  `passive_army` int(10) NOT NULL,
  `color` varchar(8) NOT NULL,
  `default_land_name` varchar(512) NOT NULL,
  `last_load` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE IF NOT EXISTS `analytics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marketing_slug` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_key` int(10) unsigned NOT NULL,
  `username` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `world_key` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_request`
--

CREATE TABLE IF NOT EXISTS `ip_request` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(64) NOT NULL,
  `request` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `land`
--

CREATE TABLE IF NOT EXISTS `land` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coord_slug` varchar(8) NOT NULL,
  `lat` int(4) NOT NULL,
  `lng` int(4) NOT NULL,
  `world_key` int(10) unsigned NOT NULL,
  `account_key` int(10) unsigned NOT NULL,
  `claimed` int(1) NOT NULL,
  `land_name` varchar(512) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `type` varchar(64) NOT NULL,
  `color` varchar(8) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27945 ;

-- --------------------------------------------------------

--
-- Table structure for table `action_log`
--

CREATE TABLE IF NOT EXISTS `action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active_account_key` int(10) unsigned NOT NULL,
  `passive_account_key` int(10) unsigned NOT NULL,
  `action` varchar(32) NOT NULL,
  `result` varchar(32) NOT NULL,
  `world_key` int(10) unsigned NOT NULL,
  `coord_slug` varchar(8) NOT NULL,
  `name_at_action` varchar(512) NOT NULL,
  `details` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `ip` varchar(64) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `world`
--

CREATE TABLE IF NOT EXISTS `world` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(126) NOT NULL,
  `land_size` int(4) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
