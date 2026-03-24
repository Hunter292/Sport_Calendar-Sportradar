-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 04:21 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sport_calendar`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_event` (IN `sport_id` INT, IN `venue_id` INT, IN `competition_id` INT, IN `datee` VARCHAR(10), IN `timee` VARCHAR(8), IN `status` VARCHAR(50), IN `inserts` TEXT, IN `description` TEXT)  DETERMINISTIC COMMENT 'insert event and teams_playing' BEGIN
DECLARE e_id,pos,pos_p INT DEFAULT 0;
DECLARE abort BOOLEAN DEFAULT FALSE;
DECLARE EXIT HANDLER FOR SQLEXCEPTION 
    BEGIN
        ROLLBACK;
        SET abort=TRUE;
    END;
START TRANSACTION;
	INSERT INTO event values(NULL,sport_id,venue_id,competition_id,NULL,datee,timee,status,description);
    SELECT event_id into e_id FROM event WHERE _venue_id=venue_id and _sport_id=sport_id and date=datee and time=timee and 		_competition_id=competition_id ORDER BY event_id desc LIMIT 1;
    teams_Loop: LOOP
    	SET pos=LOCATE(',',inserts,pos_p+1);
        IF pos=0 THEN LEAVE teams_Loop; END IF;
        INSERT INTO teams_playing VALUES(NULL,e_id,SUBSTR(inserts,pos_p+1,pos-pos_p-1));
        SET pos_p=pos;
    END LOOP;
IF !abort THEN COMMIT; END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

CREATE TABLE `competition` (
  `competition_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `competition`
--

INSERT INTO `competition` (`competition_id`, `name`) VALUES
(1, 'sparing'),
(2, 'Extraklasa 2026 season');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `_sport_id` int(11) NOT NULL,
  `_venue_id` int(11) NOT NULL,
  `_competition_id` int(11) NOT NULL,
  `_winner` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(25) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`event_id`, `_sport_id`, `_venue_id`, `_competition_id`, `_winner`, `date`, `time`, `status`, `description`) VALUES
(1, 1, 3, 2, NULL, '2026-04-30', '12:00:00', 'scheduled', ''),
(2, 1, 4, 2, 3, '2026-03-01', '12:00:00', 'played', ''),
(3, 2, 3, 1, NULL, '2026-04-30', '13:00:00', 'scheduled', ''),
(10, 2, 4, 1, NULL, '2026-03-28', '13:00:00', 'scheduled', ''),
(11, 2, 4, 1, NULL, '2026-03-31', '12:00:00', 'scheduled', ''),
(12, 1, 3, 2, 4, '2026-03-03', '13:03:00', 'played', ''),
(13, 1, 3, 2, NULL, '2026-03-25', '12:00:00', 'scheduled', ''),
(14, 2, 3, 1, NULL, '2026-03-04', '12:00:00', 'played', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `country`, `city`) VALUES
(1, 'Poland', 'Warsaw'),
(2, 'Germany', 'Berlin'),
(3, 'Temeria', 'Wyzima');

-- --------------------------------------------------------

--
-- Table structure for table `sport`
--

CREATE TABLE `sport` (
  `sport_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sport`
--

INSERT INTO `sport` (`sport_id`, `name`) VALUES
(1, 'Football'),
(2, 'HEMA');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL,
  `_location_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`team_id`, `_location_id`, `name`) VALUES
(3, 1, 'RKS Placeholder'),
(4, 2, 'FC Wyzima'),
(5, 2, 'German team'),
(6, 3, 'Player 1'),
(7, 2, 'Player 2');

-- --------------------------------------------------------

--
-- Table structure for table `teams_playing`
--

CREATE TABLE `teams_playing` (
  `id` int(11) NOT NULL,
  `_event_id` int(11) NOT NULL,
  `_team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teams_playing`
--

INSERT INTO `teams_playing` (`id`, `_event_id`, `_team_id`) VALUES
(1, 1, 4),
(2, 1, 5),
(3, 2, 5),
(4, 2, 3),
(5, 3, 6),
(6, 3, 7),
(9, 10, 6),
(10, 10, 7),
(11, 11, 3),
(12, 12, 4),
(13, 12, 5),
(14, 13, 3),
(15, 14, 6),
(16, 14, 7);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `login`, `password`) VALUES
(1, 'test', '$2y$10$9rkXtVuvXLP1x/r38k.N/eKoG/C8oSP53K/RiV1t7c9kPSZjbGfuu');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `venue_id` int(11) NOT NULL,
  `_location_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`venue_id`, `_location_id`, `name`, `address`, `capacity`) VALUES
(3, 1, 'Polish Army Stadium', '', 31103),
(4, 2, 'German stadium', '', 10000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`competition_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `_venue_id` (`_venue_id`),
  ADD KEY `_sport_id` (`_sport_id`),
  ADD KEY `_competition_id` (`_competition_id`),
  ADD KEY `_winner` (`_winner`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `sport`
--
ALTER TABLE `sport`
  ADD PRIMARY KEY (`sport_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `_location_id` (`_location_id`);

--
-- Indexes for table `teams_playing`
--
ALTER TABLE `teams_playing`
  ADD PRIMARY KEY (`id`,`_event_id`,`_team_id`),
  ADD KEY `_event_id` (`_event_id`),
  ADD KEY `_team_id` (`_team_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`venue_id`),
  ADD KEY `_location_id` (`_location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `competition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sport`
--
ALTER TABLE `sport`
  MODIFY `sport_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teams_playing`
--
ALTER TABLE `teams_playing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `venue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`_competition_id`) REFERENCES `competition` (`competition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`_venue_id`) REFERENCES `venue` (`venue_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_3` FOREIGN KEY (`_sport_id`) REFERENCES `sport` (`sport_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_4` FOREIGN KEY (`_winner`) REFERENCES `team` (`team_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`_location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `teams_playing`
--
ALTER TABLE `teams_playing`
  ADD CONSTRAINT `teams_playing_ibfk_1` FOREIGN KEY (`_event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `teams_playing_ibfk_2` FOREIGN KEY (`_team_id`) REFERENCES `team` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue`
--
ALTER TABLE `venue`
  ADD CONSTRAINT `venue_ibfk_1` FOREIGN KEY (`_location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `Change_played_events` ON SCHEDULE EVERY 1 DAY STARTS '2026-03-21 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
UPDATE event SET event.status="played" WHERE event.status="scheduled" and event.date<curdate();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
