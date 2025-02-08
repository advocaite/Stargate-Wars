-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2016 at 11:57 AM
-- Server version: 5.6.14
-- PHP Version: 5.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sgwars2`
--

-- --------------------------------------------------------

--
-- Table structure for table `armory`
--

CREATE TABLE IF NOT EXISTS `armory` (
  `wid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `isDefense` int(11) NOT NULL,
  `cash_cost` int(11) NOT NULL,
  `unit_cost` int(11) NOT NULL,
  `weaponName` varchar(64) NOT NULL,
  `weaponPower` int(11) NOT NULL,
  `requireTrained` int(11) NOT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `uid` int(11) NOT NULL,
  `inbank` int(11) NOT NULL,
  `onHand` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`uid`, `inbank`, `onHand`) VALUES
(1, 0, 250000);

-- --------------------------------------------------------

--
-- Table structure for table `planets`
--

CREATE TABLE IF NOT EXISTS `planets` (
  `uid` int(11) NOT NULL,
  `text` varchar(64) NOT NULL,
  `plnt_name` varchar(64) NOT NULL,
  `income_bonus` int(11) NOT NULL,
  `up_bonus` int(11) NOT NULL,
  `isHome` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `plnt_size` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `planets`
--

INSERT INTO `planets` (`uid`, `text`, `plnt_name`, `income_bonus`, `up_bonus`, `isHome`, `pid`, `plnt_size`) VALUES
(1, '', 'root', 0, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `planetsize`
--

CREATE TABLE IF NOT EXISTS `planetsize` (
  `text` varchar(255) NOT NULL,
  `size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `power`
--

CREATE TABLE IF NOT EXISTS `power` (
  `uid` int(11) NOT NULL,
  `overall` bigint(255) NOT NULL,
  `mil_atk` bigint(255) NOT NULL,
  `mil_def` bigint(255) NOT NULL,
  `mil_cov` bigint(255) NOT NULL,
  `mil_anti` bigint(255) NOT NULL,
  `mil_total` bigint(255) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `power`
--

INSERT INTO `power` (`uid`, `overall`, `mil_atk`, `mil_def`, `mil_cov`, `mil_anti`, `mil_total`) VALUES
(1, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `race`
--

CREATE TABLE IF NOT EXISTS `race` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `r_name` varchar(32) NOT NULL,
  `income_bonus` int(11) NOT NULL,
  `up_bonus` int(11) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `race`
--

INSERT INTO `race` (`rid`, `r_name`, `income_bonus`, `up_bonus`) VALUES
(1, 'Ancient', 0, 0),
(2, 'Nox', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `uid` int(11) NOT NULL,
  `overall` bigint(255) NOT NULL,
  `mil_atk` bigint(255) NOT NULL,
  `mil_def` bigint(255) NOT NULL,
  `mil_cov` bigint(255) NOT NULL,
  `mil_anti` bigint(255) NOT NULL,
  `mil_total` bigint(255) NOT NULL,
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rank`
--

INSERT INTO `rank` (`uid`, `overall`, `mil_atk`, `mil_def`, `mil_cov`, `mil_anti`, `mil_total`) VALUES
(1, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `technology`
--

CREATE TABLE IF NOT EXISTS `technology` (
  `uid` int(11) NOT NULL,
  `income` int(11) NOT NULL,
  `unitProd` int(11) NOT NULL,
  `uppl` int(11) NOT NULL,
  `cov_lvl` int(11) NOT NULL,
  `anti_lvl` int(11) NOT NULL,
  `covert` int(11) NOT NULL,
  `anticovert` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `auEffect` int(11) NOT NULL,
  `auRes` int(11) NOT NULL,
  `auSteal` int(11) NOT NULL,
  `acuEffect` int(11) NOT NULL,
  `acuRes` int(11) NOT NULL,
  `duSteal` int(11) NOT NULL,
  `cuEffect` int(11) NOT NULL,
  `cuRes` int(11) NOT NULL,
  `duEffect` int(11) NOT NULL,
  `duRes` int(11) NOT NULL,
  `ascend` int(11) NOT NULL,
  `galaxy` int(11) NOT NULL,
  `pDef` int(11) NOT NULL,
  `puCap` int(11) NOT NULL,
  `pmCap` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `technology`
--

INSERT INTO `technology` (`uid`, `income`, `unitProd`, `uppl`, `cov_lvl`, `anti_lvl`, `covert`, `anticovert`, `attack`, `defense`, `auEffect`, `auRes`, `auSteal`, `acuEffect`, `acuRes`, `duSteal`, `cuEffect`, `cuRes`, `duEffect`, `duRes`, `ascend`, `galaxy`, `pDef`, `puCap`, `pmCap`) VALUES
(1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `unitcost`
--

CREATE TABLE IF NOT EXISTS `unitcost` (
  `rid` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `superAttack` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `superDefense` int(11) NOT NULL,
  `covert` int(11) NOT NULL,
  `superCovert` int(11) NOT NULL,
  `anticovert` int(11) NOT NULL,
  `superAnticovert` int(11) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unitnames`
--

CREATE TABLE IF NOT EXISTS `unitnames` (
  `rid` int(11) NOT NULL,
  `attack` varchar(64) NOT NULL,
  `superAttack` varchar(64) NOT NULL,
  `attackMercs` varchar(64) NOT NULL,
  `defense` varchar(64) NOT NULL,
  `superDefense` varchar(64) NOT NULL,
  `defenseMercs` varchar(64) NOT NULL,
  `covert` varchar(64) NOT NULL,
  `superCovert` varchar(64) NOT NULL,
  `anticovert` varchar(64) NOT NULL,
  `superAnticovert` varchar(64) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `uid` int(11) NOT NULL,
  `attack` int(11) NOT NULL,
  `superAttack` int(11) NOT NULL,
  `attackMercs` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `superDefense` int(11) NOT NULL,
  `defenseMercs` int(11) NOT NULL,
  `untrained` int(11) NOT NULL,
  `miners` int(11) NOT NULL,
  `lifers` int(11) NOT NULL,
  `covert` int(11) NOT NULL,
  `superCovert` int(11) NOT NULL,
  `anticovert` int(11) NOT NULL,
  `superAnticovert` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`uid`, `attack`, `superAttack`, `attackMercs`, `defense`, `superDefense`, `defenseMercs`, `untrained`, `miners`, `lifers`, `covert`, `superCovert`, `anticovert`, `superAnticovert`) VALUES
(1, 0, 0, 0, 0, 0, 0, 250, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `userdata`
--

CREATE TABLE IF NOT EXISTS `userdata` (
  `uid` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `actionTurns` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `uname` varchar(64) NOT NULL,
  `cid` int(11) NOT NULL,
  `progress` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userdata`
--

INSERT INTO `userdata` (`uid`, `link`, `actionTurns`, `rid`, `uname`, `cid`, `progress`) VALUES
(1, 'vopmf1398332084', 250, 1, '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `allyid` int(11) NOT NULL,
  `lastLogin` int(11) NOT NULL,
  `arank` int(11) NOT NULL,
  `ip` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alevel` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `uname`, `email`, `allyid`, `lastLogin`, `arank`, `ip`, `password`, `alevel`) VALUES
(1, 'root', 'rodneywowwow@gmail.com', 0, 0, 0, 0, '3a1550505eaac003513a142b13872405', 1);

-- --------------------------------------------------------

--
-- Table structure for table `weapons`
--

CREATE TABLE IF NOT EXISTS `weapons` (
  `wid` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `quanity` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
