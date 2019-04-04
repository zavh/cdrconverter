-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2019 at 10:31 PM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.1.27-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webcdr`
--

-- --------------------------------------------------------

--
-- Table structure for table `webcdr`
--

CREATE TABLE `webcdr` (
  `csn` bigint(10) UNSIGNED NOT NULL,
  `ans_time` bigint(20) NOT NULL,
  `end_time` bigint(20) NOT NULL,
  `conversation_time` int(11) NOT NULL,
  `caller_number` varchar(50) NOT NULL,
  `called_number` varchar(50) NOT NULL,
  `trunk_group_in` varchar(16) NOT NULL,
  `trunk_group_out` varchar(16) NOT NULL,
  `fractionbit3` int(3) NOT NULL,
  `connected_number` varchar(100) NOT NULL,
  `dial_number` varchar(100) NOT NULL,
  `caller_number_before_change` varchar(100) NOT NULL,
  `called_number_before_change` varchar(100) NOT NULL,
  `caller_seize_duration` int(10) NOT NULL,
  `called_seize_duration` int(10) NOT NULL,
  `Incoming Route ID` varchar(16) NOT NULL,
  `Outgoing Route ID` varchar(16) NOT NULL,
  `alerting time` bigint(20) NOT NULL,
  `caller physical number` varchar(50) NOT NULL,
  `callee physical number` varchar(50) NOT NULL,
  `Caller_call_id` varchar(64) NOT NULL,
  `Called_call_id` varchar(64) NOT NULL,
  `processed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `webcdr`
--

TRUNCATE TABLE `webcdr`;
--
-- Dumping data for table `webcdr`
--

INSERT INTO `webcdr` (`csn`, `ans_time`, `end_time`, `conversation_time`, `caller_number`, `called_number`, `trunk_group_in`, `trunk_group_out`, `fractionbit3`, `connected_number`, `dial_number`, `caller_number_before_change`, `called_number_before_change`, `caller_seize_duration`, `called_seize_duration`, `Incoming Route ID`, `Outgoing Route ID`, `alerting time`, `caller physical number`, `callee physical number`, `Caller_call_id`, `Called_call_id`, `processed`) VALUES
(9481539, 1538244004000, 1538244008000, 4, '971566708179', '002201760894977', 'CUS-TASCOM', 'SUP-BTRAC_20', 17, '002201760894977', '7564118801760894977', '971566708179', '118801760894977', 16757, 3594, 'CUS-TASCOM', 'SUP-BTRAC', 1538244003996, '971566708179', '002201760894977', '2cb0afd863616c6c01134d63@119.81.202.84', '3a4555cd63616c6c05a1e9d7@103.245.143.11', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `webcdr`
--
ALTER TABLE `webcdr`
  ADD PRIMARY KEY (`csn`),
  ADD UNIQUE KEY `ans_time` (`ans_time`,`end_time`,`conversation_time`,`caller_number`,`called_number`,`trunk_group_in`,`trunk_group_out`,`dial_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `webcdr`
--
ALTER TABLE `webcdr`
  MODIFY `csn` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
