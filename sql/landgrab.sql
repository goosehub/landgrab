-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2017 at 12:06 PM
-- Server version: 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `landgrab`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_key` int(10) UNSIGNED NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `active_account` int(1) UNSIGNED NOT NULL,
  `tutorial` int(10) UNSIGNED NOT NULL,
  `nation_name` varchar(256) NOT NULL,
  `nation_flag` varchar(256) NOT NULL,
  `leader_portrait` varchar(256) NOT NULL,
  `government` int(10) UNSIGNED NOT NULL,
  `last_government_switch` timestamp NOT NULL,
  `tax_rate` int(10) UNSIGNED NOT NULL,
  `military_budget` int(10) UNSIGNED NOT NULL,
  `entitlements_budget` int(10) UNSIGNED NOT NULL,
  `weariness` int(10) UNSIGNED NOT NULL,
  `color` varchar(8) NOT NULL,
  `last_load` varchar(32) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(10) UNSIGNED NOT NULL,
  `marketing_slug` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_key` int(10) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `color` varchar(8) NOT NULL,
  `message` text NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `embassy`
--

CREATE TABLE `embassy` (
  `id` int(10) UNSIGNED NOT NULL,
  `land_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sanctions`
--

CREATE TABLE `sanctions` (
  `id` int(10) UNSIGNED NOT NULL,
  `land_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ip_request`
--

CREATE TABLE `ip_request` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(64) NOT NULL,
  `request` varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `land`
--

CREATE TABLE `land` (
  `id` int(10) UNSIGNED NOT NULL,
  `coord_slug` varchar(8) NOT NULL,
  `lat` int(4) NOT NULL,
  `lng` int(4) NOT NULL,
  `world_key` int(10) UNSIGNED NOT NULL,
  `account_key` int(10) UNSIGNED NOT NULL,
  `land_type` int(10) UNSIGNED NOT NULL,
  `capitol` int(1) NOT NULL,
  `land_name` varchar(512) NOT NULL,
  `content` varchar(1024) NOT NULL,
  `color` varchar(8) NOT NULL,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `land_modifier`
--

CREATE TABLE `land_modifier` (
  `id` int(10) UNSIGNED NOT NULL,
  `land_key` int(10) UNSIGNED NOT NULL,
  `modify_effect_key` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `modify_effect`
--

CREATE TABLE `modify_effect` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(256) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL,
  `is_land_upgrade` int(1) NOT NULL,
  `is_embassy` int(1) NOT NULL,
  `is_sanctions` int(1) NOT NULL,
  `population` int(11) NOT NULL,
  `culture` int(11) NOT NULL,
  `gdp` int(11) NOT NULL,
  `treasury` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `military` int(11) NOT NULL,
  `support` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(256) NOT NULL,
  `facebook_id` int(16) NOT NULL,
  `email` varchar(256) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `ab_test` varchar(256) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `world`
--

CREATE TABLE `world` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(126) NOT NULL,
  `land_size` int(4) NOT NULL,
  `last_winner_account_key` int(10) UNSIGNED NOT NULL,
  `last_winner_land_count` int(10) UNSIGNED NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `embassy`
--
ALTER TABLE `embassy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ip_request`
--
ALTER TABLE `ip_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `land`
--
ALTER TABLE `land`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `land_modifier`
--
ALTER TABLE `land_modifier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modify_effect`
--
ALTER TABLE `modify_effect`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `world`
--
ALTER TABLE `world`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `embassy`
--
ALTER TABLE `embassy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ip_request`
--
ALTER TABLE `ip_request`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `land`
--
ALTER TABLE `land`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42841;
--
-- AUTO_INCREMENT for table `land_modifier`
--
ALTER TABLE `land_modifier`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `modify_effect`
--
ALTER TABLE `modify_effect`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `world`
--
ALTER TABLE `world`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
