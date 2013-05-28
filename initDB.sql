-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2013 at 11:01 PM
-- Server version: 5.5.31
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `prjband`
--
CREATE DATABASE `prjband` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `prjband`;

-- --------------------------------------------------------

--
-- Table structure for table `acct`
--

CREATE TABLE IF NOT EXISTS `acct` (
  `id` tinyint(4) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `band_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `acct`
--

INSERT INTO `acct` (`id`, `user`, `pass`, `band_id`) VALUES
(1, 'tywong', 'sosad', 0),
(2, 'wendy', 'hihi', 0),
(3, 'eddy', '123', 0),
(4, 'mtyiu', '123', 0),
(5, 'derek', '123', 0),
(6, 'mole', 'wuar', 0);

-- --------------------------------------------------------

--
-- Table structure for table `band`
--

CREATE TABLE IF NOT EXISTS `band` (
  `band_id` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `admin` varchar(255) NOT NULL,
  `no_player` tinyint(4) NOT NULL,
  `content` varchar(255) NOT NULL,
  `player1` varchar(255) NOT NULL,
  `player2` varchar(255) NOT NULL,
  `player3` varchar(255) NOT NULL,
  PRIMARY KEY (`band_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mixer`
--

CREATE TABLE IF NOT EXISTS `mixer` (
  `band` tinyint(4) NOT NULL,
  `user` varchar(255) NOT NULL,
  `volume` tinyint(4) NOT NULL DEFAULT '100',
  `pan` tinyint(4) NOT NULL DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `music_info`
--

CREATE TABLE IF NOT EXISTS `music_info` (
  `band_id` tinyint(4) NOT NULL,
  `song_name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `tempo` int(11) NOT NULL,
  `song_key` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
