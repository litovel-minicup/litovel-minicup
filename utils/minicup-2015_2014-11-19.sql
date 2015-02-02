-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Stř 19. lis 2014, 22:42
-- Verze serveru: 5.5.40-0ubuntu1
-- Verze PHP: 5.5.12-2ubuntu4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `minicup-2015`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
`id` int(11) NOT NULL,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(1, 'mladší', 'mladsi'),
(2, 'starší', 'starsi');

-- --------------------------------------------------------

--
-- Struktura tabulky `day`
--

CREATE TABLE IF NOT EXISTS `day` (
`id` int(11) NOT NULL,
  `day` date NOT NULL,
  `year_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `day`
--

INSERT INTO `day` (`id`, `day`, `year_id`) VALUES
(1, '2015-05-22', 3),
(2, '2015-05-23', 3),
(3, '2014-05-24', 3);

-- --------------------------------------------------------

--
-- Struktura tabulky `match`
--

CREATE TABLE IF NOT EXISTS `match` (
`id` int(11) NOT NULL,
  `match_term_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `score_home` int(11) DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `match`
--

INSERT INTO `match` (`id`, `match_term_id`, `category_id`, `home_team_id`, `away_team_id`, `score_home`, `score_away`) VALUES
(2, 1, 1, 2, 1, 10, 16),
(3, 2, 1, 3, 1, NULL, NULL),
(4, 1, 1, 4, 2, NULL, NULL),
(5, 2, 1, 2, 1, 10, 16),
(6, 1, 1, 3, 1, NULL, NULL),
(7, 2, 1, 4, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `match_term`
--

CREATE TABLE IF NOT EXISTS `match_term` (
`id` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `day_id` int(11) NOT NULL,
  `location` varchar(50) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `match_term`
--

INSERT INTO `match_term` (`id`, `start`, `end`, `day_id`, `location`) VALUES
(1, '14:00:00', '14:30:00', 1, 'Sokolovna'),
(2, '14:30:00', '15:00:00', 1, 'Sokolovna'),
(3, '15:00:00', '15:30:00', 1, 'Sokolovna'),
(4, '15:30:00', '16:00:00', 1, 'Sokolovna'),
(5, '16:00:00', '16:30:00', 1, 'Sokolovna'),
(6, '16:30:00', '17:00:00', 1, 'Sokolovna'),
(7, '17:00:00', '17:30:00', 1, 'Sokolovna'),
(8, '17:30:00', '18:00:00', 1, 'Sokolovna');

-- --------------------------------------------------------

--
-- Struktura tabulky `online_report`
--

CREATE TABLE IF NOT EXISTS `online_report` (
`id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `type` char(20) COLLATE utf8_czech_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `online_report`
--

INSERT INTO `online_report` (`id`, `match_id`, `message`, `type`, `updated`, `added`) VALUES
(23, 4, 'Střílí a dává góoool!', 'goal', '2014-11-15 21:37:41', '2014-11-15 22:37:41'),
(24, 4, 'Obrovská šance domácích!', 'info', '2014-11-15 22:59:56', '2014-11-15 23:59:56'),
(25, 4, 'Další šance!', 'goal', '2014-11-15 23:00:23', '2014-11-16 00:00:23'),
(26, 4, 'Neskutečné!', 'goal', '2014-11-15 23:00:42', '2014-11-16 00:00:42');

-- --------------------------------------------------------

--
-- Struktura tabulky `team`
--

CREATE TABLE IF NOT EXISTS `team` (
`id` int(11) NOT NULL,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL,
  `order` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `team`
--

INSERT INTO `team` (`id`, `name`, `slug`, `order`, `category_id`) VALUES
(1, 'Foo foos', 'foo-foos', 1, 2),
(2, 'Bar bars', 'bar-bars', 1, 1),
(3, 'Foo bar', 'foo-bar', 1, 2),
(4, 'Bar foo', 'bar-foo', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(11) NOT NULL,
  `username` text COLLATE utf8_czech_ci NOT NULL,
  `password_hash` text COLLATE utf8_czech_ci NOT NULL,
  `role` text COLLATE utf8_czech_ci NOT NULL,
  `fullname` text COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `username`, `password_hash`, `role`, `fullname`) VALUES
(4, 'joe', '$2y$10$W27OfVuYx/qdL6SI5mAOFOKjiqS3JIYvXqKEkIeMHzzUvDBZOdPFi', 'admin', 'Joe Kolář'),
(5, 'Kathy', '$2y$10$8oNfu69w8.vvyrFwZL9rYuFxwkygbPmvINuRU5c2FUMhahSiSIlaW', 'moderator', 'Kathy někdo');

-- --------------------------------------------------------

--
-- Struktura tabulky `year`
--

CREATE TABLE IF NOT EXISTS `year` (
`id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `name` text COLLATE utf8_czech_ci,
  `actual` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `year`
--

INSERT INTO `year` (`id`, `year`, `name`, `actual`) VALUES
(1, 2013, '2013', 0),
(2, 2014, '2014', 0),
(3, 2015, '2015', 1);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `slug` (`slug`);

--
-- Klíče pro tabulku `day`
--
ALTER TABLE `day`
 ADD PRIMARY KEY (`id`), ADD KEY `year_id` (`year_id`);

--
-- Klíče pro tabulku `match`
--
ALTER TABLE `match`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD KEY `AI_id` (`id`), ADD KEY `home_team_id` (`home_team_id`), ADD KEY `away_team_id` (`away_team_id`), ADD KEY `match_term_id` (`match_term_id`), ADD KEY `category_id` (`category_id`);

--
-- Klíče pro tabulku `match_term`
--
ALTER TABLE `match_term`
 ADD PRIMARY KEY (`id`), ADD KEY `day_id` (`day_id`);

--
-- Klíče pro tabulku `online_report`
--
ALTER TABLE `online_report`
 ADD PRIMARY KEY (`id`), ADD KEY `match_id` (`match_id`);

--
-- Klíče pro tabulku `team`
--
ALTER TABLE `team`
 ADD PRIMARY KEY (`id`), ADD KEY `category_id` (`category_id`);

--
-- Klíče pro tabulku `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`);

--
-- Klíče pro tabulku `year`
--
ALTER TABLE `year`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pro tabulku `day`
--
ALTER TABLE `day`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `match`
--
ALTER TABLE `match`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pro tabulku `match_term`
--
ALTER TABLE `match_term`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pro tabulku `online_report`
--
ALTER TABLE `online_report`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pro tabulku `team`
--
ALTER TABLE `team`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pro tabulku `year`
--
ALTER TABLE `year`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `day`
--
ALTER TABLE `day`
ADD CONSTRAINT `day_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`);

--
-- Omezení pro tabulku `match`
--
ALTER TABLE `match`
ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
ADD CONSTRAINT `match_away_team_id` FOREIGN KEY (`away_team_id`) REFERENCES `team` (`id`),
ADD CONSTRAINT `match_home_team_id` FOREIGN KEY (`home_team_id`) REFERENCES `team` (`id`),
ADD CONSTRAINT `match_ibfk_1` FOREIGN KEY (`match_term_id`) REFERENCES `match_term` (`id`);

--
-- Omezení pro tabulku `match_term`
--
ALTER TABLE `match_term`
ADD CONSTRAINT `day_id` FOREIGN KEY (`day_id`) REFERENCES `day` (`id`);

--
-- Omezení pro tabulku `online_report`
--
ALTER TABLE `online_report`
ADD CONSTRAINT `online_report_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match` (`id`);

--
-- Omezení pro tabulku `team`
--
ALTER TABLE `team`
ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
