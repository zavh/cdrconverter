-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2019 at 10:36 PM
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
-- Table structure for table `trunkgroup`
--

CREATE TABLE `trunkgroup` (
  `trunkid` int(11) NOT NULL,
  `trunk_name` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trunkgroup`
--

INSERT INTO `trunkgroup` (`trunkid`, `trunk_name`) VALUES
(1, 'CUS-TASCOM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `trunkgroup`
--
ALTER TABLE `trunkgroup`
  ADD PRIMARY KEY (`trunkid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trunkgroup`
--
ALTER TABLE `trunkgroup`
  MODIFY `trunkid` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
