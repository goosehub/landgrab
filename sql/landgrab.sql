-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2016 at 03:58 AM
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
  `tutorial` int(10) unsigned NOT NULL,
  `nation_name` varchar(256) NOT NULL,
  `nation_flag` varchar(256) NOT NULL,
  `leader_portrait` varchar(256) NOT NULL,
  `government` int(10) unsigned NOT NULL,
  `last_government_switch` timestamp NOT NULL,
  `tax_rate` int(10) unsigned NOT NULL,
  `military_budget` int(10) unsigned NOT NULL,
  `entitlements_budget` int(10) unsigned NOT NULL,
  `war_weariness` int(10) unsigned NOT NULL,
  `color` varchar(8) NOT NULL,
  `last_load` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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
  `land_type` int(10) unsigned NOT NULL,
  `capitol` int(1) NOT NULL,
  `land_name` varchar(512) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `color` varchar(8) NOT NULL,
  `modified` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15121 ;

-- --------------------------------------------------------

--
-- Table structure for table `land_modifier`
--

CREATE TABLE IF NOT EXISTS `land_modifier` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `land_key` int(10) unsigned NOT NULL,
  `modify_effect_key` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=218 ;

-- --------------------------------------------------------

--
-- Table structure for table `modify_effect`
--

CREATE TABLE IF NOT EXISTS `modify_effect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL,
  `is_land_upgrade` int(1) NOT NULL,
  `population` int(11) NOT NULL,
  `gdp` int(11) NOT NULL,
  `treasury` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `military` int(11) NOT NULL,
  `support` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `modify_effect` (`id`, `name`, `sort_order`, `is_land_upgrade`, `population`, `gdp`, `treasury`, `defense`, `military`, `support`, `created`) VALUES
(1, 'unclaimed', 0, 1, 0, 0, 0, 0, 0, 0, '2016-11-08 10:26:05'),
(2, 'village', 1, 1, 1, 1, 0, 0, 0, 0, '2016-11-08 10:26:51'),
(3, 'town', 2, 1, 100, 10, 0, 2, 0, 0, '2016-11-08 10:27:27'),
(4, 'city', 3, 1, 1000, 100, 0, 3, 0, 0, '2016-11-08 10:27:58'),
(5, 'metropolis', 4, 1, 10000, 1000, 0, 4, 0, 0, '2016-11-08 10:28:34'),
(6, 'fortification', 2, 1, 10, 1, -10, 3, 1, 0, '2016-11-24 06:24:53'),
(10, 'capitol', 5, 1, 500, 50, 0, 5, 0, 5, '2016-11-08 10:29:47'),
(11, 'skyscraper', 10, 0, 50, 5, -10, 0, 0, 1, '2016-11-08 10:45:22'),
(12, 'factory', 9, 0, 100, 5, -10, 0, 1, 0, '2016-11-08 10:45:12'),
(14, 'hospital', 7, 0, 500, 0, -15, 0, 0, 1, '2016-11-08 10:33:05'),
(15, 'military_base', 6, 0, 0, 0, -25, 2, 10, 0, '2016-11-08 10:34:10'),
(16, 'school', 8, 0, 100, 10, -20, 0, 0, 2, '2016-11-08 10:35:48');