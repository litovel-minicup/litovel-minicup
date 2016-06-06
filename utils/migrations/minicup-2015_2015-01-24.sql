-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vytvořeno: Sob 24. led 2015, 22:50
-- Verze serveru: 5.5.41-0ubuntu0.14.10.1
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
  `year_id` int(11) DEFAULT NULL,
  `name` char(30) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(30) COLLATE utf8_czech_ci NOT NULL,
  `default` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `year_id`, `name`, `slug`, `default`) VALUES
(1, 1, 'mladší', 'mladsi', 1),
(2, 1, 'starší', 'starsi', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `day`
--

CREATE TABLE IF NOT EXISTS `day` (
`id` int(11) NOT NULL,
  `day` date NOT NULL,
  `year_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `day`
--

INSERT INTO `day` (`id`, `day`, `year_id`) VALUES
(77, '2015-05-23', 1),
(78, '2015-05-24', 1),
(79, '2015-05-25', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `match`
--

CREATE TABLE IF NOT EXISTS `match` (
`id` int(11) NOT NULL,
  `match_term_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `home_team_info_id` int(11) NOT NULL,
  `away_team_info_id` int(11) NOT NULL,
  `score_home` int(11) DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL,
  `confirmed` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `match`
--

INSERT INTO `match` (`id`, `match_term_id`, `category_id`, `home_team_info_id`, `away_team_info_id`, `score_home`, `score_away`, `confirmed`) VALUES
(1, 555, 1, 77, 76, NULL, NULL, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=587 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `match_term`
--

INSERT INTO `match_term` (`id`, `start`, `end`, `day_id`, `location`) VALUES
(554, '13:00:00', '13:30:00', 77, ''),
(555, '13:30:00', '14:00:00', 77, ''),
(556, '14:00:00', '14:30:00', 77, ''),
(557, '14:30:00', '15:00:00', 77, ''),
(558, '15:00:00', '15:30:00', 77, ''),
(559, '15:30:00', '16:00:00', 77, ''),
(560, '16:00:00', '16:30:00', 77, ''),
(561, '16:30:00', '17:00:00', 77, ''),
(562, '17:00:00', '17:30:00', 77, ''),
(563, '09:00:00', '09:30:00', 78, ''),
(564, '09:30:00', '10:00:00', 78, ''),
(565, '10:00:00', '10:30:00', 78, ''),
(566, '10:30:00', '11:00:00', 78, ''),
(567, '11:00:00', '11:30:00', 78, ''),
(568, '11:30:00', '12:00:00', 78, ''),
(569, '12:00:00', '12:30:00', 78, ''),
(570, '12:30:00', '13:00:00', 78, ''),
(571, '13:00:00', '13:30:00', 78, ''),
(572, '13:30:00', '14:00:00', 78, ''),
(573, '14:00:00', '14:30:00', 78, ''),
(574, '14:30:00', '15:00:00', 78, ''),
(575, '15:00:00', '15:30:00', 78, ''),
(576, '09:00:00', '09:30:00', 79, ''),
(577, '09:30:00', '10:00:00', 79, ''),
(578, '10:00:00', '10:30:00', 79, ''),
(579, '10:30:00', '11:00:00', 79, ''),
(580, '11:00:00', '11:30:00', 79, ''),
(581, '11:30:00', '12:00:00', 79, ''),
(582, '12:00:00', '12:30:00', 79, ''),
(583, '12:30:00', '13:00:00', 79, ''),
(584, '13:00:00', '13:30:00', 79, ''),
(585, '13:30:00', '14:00:00', 79, ''),
(586, '14:00:00', '14:30:00', 79, '');

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
  `added` datetime NOT NULL,
  `author` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `photo`
--

CREATE TABLE IF NOT EXISTS `photo` (
`id` int(11) NOT NULL,
  `path_full` text COLLATE utf8_czech_ci NOT NULL,
  `path_thumb` text COLLATE utf8_czech_ci NOT NULL,
  `captured` datetime NOT NULL,
  `author_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `photo`
--

INSERT INTO `photo` (`id`, `path_full`, `path_thumb`, `captured`, `author_id`) VALUES
(1, 'cesta_full', 'cesta_thumb', '2015-01-19 22:11:20', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `photo_tag`
--

CREATE TABLE IF NOT EXISTS `photo_tag` (
  `photo_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `photo_tag`
--

INSERT INTO `photo_tag` (`photo_id`, `tag_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `static_content`
--

CREATE TABLE IF NOT EXISTS `static_content` (
`id` int(11) NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `static_content`
--

INSERT INTO `static_content` (`id`, `content`, `updated`, `user_id`) VALUES
(1, 'Vítejte!\r\n--------\r\n\r\nMůžete používat syntax Texy!, pokud Vám vyhovuje:\r\n- třeba **tučné** písmo nebo *kurzíva*\r\n- a takto se dělá "odkaz":http://texy.info\r\n- více najdete na stránce \r\n\r\nLook at homepage:[Team:default].\r\n\r\n\r\n"syntax":[syntax]\r\n\r\n\r\nAle také můžete zůstat u HTML:\r\n- takto <b>HTML</b>\r\n- nebo i <b class=xx>úplně <i>hloupě</b>, Texy! to pořeší\r\n\r\n\r\n[syntax]: /cs/syntax', '2015-01-24 20:49:00', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
`id` int(11) NOT NULL,
  `name` text COLLATE utf8_czech_ci,
  `slug` text COLLATE utf8_czech_ci NOT NULL,
  `is_gallery` int(11) NOT NULL DEFAULT '0',
  `main_photo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `tag`
--

INSERT INTO `tag` (`id`, `name`, `slug`, `is_gallery`, `main_photo_id`) VALUES
(1, 'nějaký tag', 'nejaky-tag', 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `team`
--

CREATE TABLE IF NOT EXISTS `team` (
`id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `team_info_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `scored` int(11) NOT NULL,
  `received` int(11) NOT NULL,
  `inserted` datetime NOT NULL,
  `is_actual` int(11) NOT NULL DEFAULT '0',
  `after_match_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `team`
--

INSERT INTO `team` (`id`, `category_id`, `team_info_id`, `order`, `points`, `scored`, `received`, `inserted`, `is_actual`, `after_match_id`) VALUES
(106, 1, 76, 1, 0, 0, 0, '2015-01-19 20:49:10', 1, NULL),
(107, 1, 77, 0, 0, 0, 0, '2015-01-22 20:30:05', 1, NULL);

--
-- Spouště `team`
--
DELIMITER //
CREATE TRIGGER `team_bi` BEFORE INSERT ON `team`
 FOR EACH ROW SET NEW.inserted = NOW()
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabulky `team_info`
--

CREATE TABLE IF NOT EXISTS `team_info` (
`id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` char(30) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(30) COLLATE utf8_czech_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `team_info`
--

INSERT INTO `team_info` (`id`, `category_id`, `name`, `slug`) VALUES
(77, 1, 'jiný test', 'jiny-test'),
(76, 1, 'Test', 'test');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `username`, `password_hash`, `role`, `fullname`) VALUES
(1, 'joe', '$2y$10$HZGTs1GzZB70Bbpc2HLnFeyBYdaudLWW/gOd7CVEcpf8C4nURUVRi', 'admin', 'Joe Kolář');

-- --------------------------------------------------------

--
-- Struktura tabulky `year`
--

CREATE TABLE IF NOT EXISTS `year` (
`id` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `name` text COLLATE utf8_czech_ci,
  `actual` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `year`
--

INSERT INTO `year` (`id`, `year`, `name`, `actual`) VALUES
(1, 2015, NULL, 1);

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `category`
--
ALTER TABLE `category`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `slug` (`slug`), ADD KEY `year_id` (`year_id`);

--
-- Klíče pro tabulku `day`
--
ALTER TABLE `day`
 ADD PRIMARY KEY (`id`), ADD KEY `year_id` (`year_id`);

--
-- Klíče pro tabulku `match`
--
ALTER TABLE `match`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD KEY `AI_id` (`id`), ADD KEY `home_team_id` (`home_team_info_id`), ADD KEY `away_team_id` (`away_team_info_id`), ADD KEY `match_term_id` (`match_term_id`), ADD KEY `category_id` (`category_id`);

--
-- Klíče pro tabulku `match_term`
--
ALTER TABLE `match_term`
 ADD PRIMARY KEY (`id`), ADD KEY `day_id` (`day_id`);

--
-- Klíče pro tabulku `online_report`
--
ALTER TABLE `online_report`
 ADD PRIMARY KEY (`id`), ADD KEY `match_id` (`match_id`), ADD KEY `author` (`author`);

--
-- Klíče pro tabulku `photo`
--
ALTER TABLE `photo`
 ADD PRIMARY KEY (`id`), ADD KEY `author_id` (`author_id`);

--
-- Klíče pro tabulku `photo_tag`
--
ALTER TABLE `photo_tag`
 ADD PRIMARY KEY (`photo_id`,`tag_id`), ADD KEY `tag_id` (`tag_id`);

--
-- Klíče pro tabulku `static_content`
--
ALTER TABLE `static_content`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

--
-- Klíče pro tabulku `tag`
--
ALTER TABLE `tag`
 ADD PRIMARY KEY (`id`), ADD KEY `main_photo_id` (`main_photo_id`);

--
-- Klíče pro tabulku `team`
--
ALTER TABLE `team`
 ADD PRIMARY KEY (`id`), ADD KEY `category_id` (`category_id`), ADD KEY `team_info_id` (`team_info_id`), ADD KEY `after_match_id` (`after_match_id`);

--
-- Klíče pro tabulku `team_info`
--
ALTER TABLE `team_info`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `category_id_name_slug` (`category_id`,`name`,`slug`);

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
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=80;
--
-- AUTO_INCREMENT pro tabulku `match`
--
ALTER TABLE `match`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `match_term`
--
ALTER TABLE `match_term`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=587;
--
-- AUTO_INCREMENT pro tabulku `online_report`
--
ALTER TABLE `online_report`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pro tabulku `photo`
--
ALTER TABLE `photo`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `static_content`
--
ALTER TABLE `static_content`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `tag`
--
ALTER TABLE `tag`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `team`
--
ALTER TABLE `team`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=108;
--
-- AUTO_INCREMENT pro tabulku `team_info`
--
ALTER TABLE `team_info`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=78;
--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `year`
--
ALTER TABLE `year`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `category`
--
ALTER TABLE `category`
ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`);

--
-- Omezení pro tabulku `day`
--
ALTER TABLE `day`
ADD CONSTRAINT `day_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`);

--
-- Omezení pro tabulku `match`
--
ALTER TABLE `match`
ADD CONSTRAINT `match_ibfk_1` FOREIGN KEY (`match_term_id`) REFERENCES `match_term` (`id`),
ADD CONSTRAINT `match_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
ADD CONSTRAINT `match_ibfk_5` FOREIGN KEY (`home_team_info_id`) REFERENCES `team_info` (`id`),
ADD CONSTRAINT `match_ibfk_6` FOREIGN KEY (`away_team_info_id`) REFERENCES `team_info` (`id`);

--
-- Omezení pro tabulku `match_term`
--
ALTER TABLE `match_term`
ADD CONSTRAINT `day_id` FOREIGN KEY (`day_id`) REFERENCES `day` (`id`);

--
-- Omezení pro tabulku `online_report`
--
ALTER TABLE `online_report`
ADD CONSTRAINT `online_report_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match` (`id`),
ADD CONSTRAINT `online_report_ibfk_3` FOREIGN KEY (`author`) REFERENCES `user` (`id`);

--
-- Omezení pro tabulku `photo`
--
ALTER TABLE `photo`
ADD CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Omezení pro tabulku `photo_tag`
--
ALTER TABLE `photo_tag`
ADD CONSTRAINT `photo_tag_ibfk_1` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`),
ADD CONSTRAINT `photo_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`);

--
-- Omezení pro tabulku `static_content`
--
ALTER TABLE `static_content`
ADD CONSTRAINT `static_content_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Omezení pro tabulku `tag`
--
ALTER TABLE `tag`
ADD CONSTRAINT `tag_ibfk_1` FOREIGN KEY (`main_photo_id`) REFERENCES `photo` (`id`);

--
-- Omezení pro tabulku `team`
--
ALTER TABLE `team`
ADD CONSTRAINT `team_ibfk_1` FOREIGN KEY (`team_info_id`) REFERENCES `team_info` (`id`),
ADD CONSTRAINT `team_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
ADD CONSTRAINT `team_ibfk_3` FOREIGN KEY (`after_match_id`) REFERENCES `match` (`id`);

--
-- Omezení pro tabulku `team_info`
--
ALTER TABLE `team_info`
ADD CONSTRAINT `team_info_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
