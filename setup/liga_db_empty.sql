-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2020 at 11:17 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6
-- creates all empty liga db tables

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `liga_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `daten`
--

CREATE TABLE `daten` (
  `ID` int(11) NOT NULL,
  `SpielwochenID` int(11) NOT NULL DEFAULT 0,
  `ZuordnungID` int(11) NOT NULL,
  `TeamID` int(11) NOT NULL,
  `Anzahl` int(11) DEFAULT 0,
  `Punkte` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `kommentare`
--

CREATE TABLE `kommentare` (
  `ID` int(11) NOT NULL,
  `Teaser` varchar(255) NOT NULL,
  `Kommentar` text DEFAULT NULL,
  `Datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `pwdhash` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `Liga` int(11) NOT NULL,
  `TeamID` int(11) NOT NULL,
  `userGroup` varchar(64) COLLATE latin1_german2_ci DEFAULT NULL,
  `token` varchar(255) COLLATE latin1_german2_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ligen`
--

CREATE TABLE `ligen` (
  `ID` int(11) NOT NULL,
  `LigaName` varchar(255) DEFAULT NULL,
  `AktSaisonID` int(11) DEFAULT 0,
  `Kommentar` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mak`
--

CREATE TABLE `mak` (
  `SpielWochenID` int(11) NOT NULL,
  `TeamID` int(11) NOT NULL,
  `MAK` float DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `saisons`
--

CREATE TABLE `saisons` (
  `ID` int(11) NOT NULL,
  `SaisonBezeichnung` varchar(255) DEFAULT NULL,
  `SaisonBegin` date DEFAULT NULL,
  `SaisonEnde` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `spielwochen`
--

CREATE TABLE `spielwochen` (
  `ID` int(11) NOT NULL,
  `SaisonID` int(11) DEFAULT NULL,
  `SpielwochenNr` varchar(25) DEFAULT NULL,
  `Stichtag` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teameinteilung`
--

CREATE TABLE `teameinteilung` (
  `ID` int(11) NOT NULL,
  `LigaID` int(11) DEFAULT NULL,
  `SaisonID` int(11) DEFAULT NULL,
  `TeamID` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `ID` int(11) NOT NULL,
  `Teamname` varchar(255) NOT NULL,
  `FOE` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `zuordnung`
--

CREATE TABLE `zuordnung` (
  `ID` int(11) NOT NULL,
  `Kurzbezeichnung` varchar(255) NOT NULL,
  `Beschreibung` varchar(255) DEFAULT NULL,
  `Gruppe` varchar(255) NOT NULL,
  `Punktewert` decimal(18,0) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daten`
--
ALTER TABLE `daten`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `SpielwochenID` (`SpielwochenID`),
  ADD KEY `IXZuordnungID` (`ZuordnungID`),
  ADD KEY `IXTeamID` (`TeamID`);

--
-- Indexes for table `kommentare`
--
ALTER TABLE `kommentare`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ligen`
--
ALTER TABLE `ligen`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `AktSaisonID` (`AktSaisonID`);

--
-- Indexes for table `mak`
--
ALTER TABLE `mak`
  ADD PRIMARY KEY (`SpielWochenID`,`TeamID`),
  ADD KEY `SpielWochenID` (`SpielWochenID`);

--
-- Indexes for table `saisons`
--
ALTER TABLE `saisons`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `spielwochen`
--
ALTER TABLE `spielwochen`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `SpielwochenNr` (`SpielwochenNr`,`SaisonID`),
  ADD KEY `SaisonID` (`SaisonID`);

--
-- Indexes for table `teameinteilung`
--
ALTER TABLE `teameinteilung`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `TeamID_2` (`TeamID`,`SaisonID`),
  ADD KEY `LigaID` (`LigaID`),
  ADD KEY `SaisonID` (`SaisonID`),
  ADD KEY `TeamID` (`TeamID`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `FOE` (`FOE`),
  ADD UNIQUE KEY `Teamname` (`Teamname`);

--
-- Indexes for table `zuordnung`
--
ALTER TABLE `zuordnung`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daten`
--
ALTER TABLE `daten`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kommentare`
--
ALTER TABLE `kommentare`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ligen`
--
ALTER TABLE `ligen`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saisons`
--
ALTER TABLE `saisons`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spielwochen`
--
ALTER TABLE `spielwochen`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teameinteilung`
--
ALTER TABLE `teameinteilung`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zuordnung`
--
ALTER TABLE `zuordnung`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
