-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2015 at 01:34 AM
-- Server version: 5.5.42-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `instrumentdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `activitylog`
--

CREATE TABLE IF NOT EXISTS `activitylog` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `action` text COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(10) NOT NULL,
  `date` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `auth`
--

CREATE TABLE IF NOT EXISTS `auth` (
  `logid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `type` int(1) NOT NULL,
  `ip` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`logid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `customername` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `customerno` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `customercontact` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `customerphone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `customeremail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lock` int(1) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `equipment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `cid` int(20) NOT NULL,
  `uid` int(20) NOT NULL,
  `iid` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `serialno` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `daterecv` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `serviceord` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `repairloan` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `repairtype` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `repairstatus` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `custupdated` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `rettocustomer` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `loanret` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `closed` int(1) NOT NULL,
  `closeddate` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `externalno` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `updateflag` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `userlevel` int(1) NOT NULL,
  `lastlogin` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `lastloginip` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `sessionid` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `useragent` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `firstname`, `lastname`, `username`, `password`, `email`, `userlevel`, `lastlogin`, `lastloginip`, `sessionid`, `useragent`) VALUES
(1, 'Admin', 'Admin', 'admin', '$2a$12$bb53a40d2649aef087733OYAt/7zRr46kFnbgIBdZLQszddKYYjlW', 'admin@admin.com', 3, '', '', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
