-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `minicup-2015`;
CREATE DATABASE `minicup-2015` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `minicup-2015`;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(1,	'mladší',	'mladsi'),
(2,	'starší',	'starsi');

DROP TABLE IF EXISTS `match`;
CREATE TABLE `match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_term_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `score_home` int(11) DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `AI_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `match` (`id`, `match_term_id`, `category_id`, `home_team_id`, `away_team_id`, `score_home`, `score_away`) VALUES
(2,	4,	1,	2,	1,	10,	16),
(3,	5,	1,	3,	1,	NULL,	NULL),
(4,	6,	1,	4,	2,	NULL,	NULL),
(5,	7,	1,	2,	1,	10,	16),
(6,	8,	1,	3,	1,	NULL,	NULL),
(7,	9,	1,	4,	2,	NULL,	NULL);

DROP TABLE IF EXISTS `match_term`;
CREATE TABLE `match_term` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `location` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `match_term` (`id`, `start`, `end`, `location`) VALUES
(3,	'2015-05-22 14:00:00',	'2015-05-22 14:30:00',	'Litovel'),
(4,	'2015-05-22 14:30:00',	'2015-05-22 15:00:00',	'Litovel'),
(5,	'2015-05-22 15:00:00',	'2015-05-22 15:30:00',	'Litovel'),
(6,	'2015-05-22 15:30:00',	'2015-05-22 16:00:00',	'Litovel'),
(7,	'2015-05-22 16:00:00',	'2015-05-22 16:30:00',	'Litovel'),
(8,	'2015-05-22 16:30:00',	'2015-05-22 17:00:00',	'Litovel'),
(9,	'2015-05-22 17:00:00',	'2015-05-22 18:00:00',	'Litovel'),
(10,	'2015-05-22 18:00:00',	'2015-05-22 18:30:00',	'Litovel'),
(11,	'2015-05-22 18:30:00',	'2015-05-22 19:00:00',	'Litovel');

DROP TABLE IF EXISTS `online_report`;
CREATE TABLE `online_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `type` char(20) COLLATE utf8_czech_ci NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `online_report` (`id`, `match_id`, `message`, `type`, `updated`, `added`) VALUES
(23,	4,	'Střílí a dává góoool!',	'goal',	'2014-11-15 21:37:41',	'2014-11-15 22:37:41'),
(24,	4,	'Obrovská šance domácích!',	'info',	'2014-11-15 22:59:56',	'2014-11-15 23:59:56'),
(25,	4,	'Další šance!',	'goal',	'2014-11-15 23:00:23',	'2014-11-16 00:00:23'),
(26,	4,	'Neskutečné!',	'goal',	'2014-11-15 23:00:42',	'2014-11-16 00:00:42');

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL,
  `order` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `team` (`id`, `name`, `slug`, `order`, `category_id`) VALUES
(1,	'Foo foos',	'foo-foos',	1,	2),
(2,	'Bar bars',	'bar-bars',	1,	1),
(3,	'Foo bar',	'foo-bar',	1,	2),
(4,	'Bar foo',	'bar-foo',	1,	1);

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text COLLATE utf8_czech_ci NOT NULL,
  `password_hash` text COLLATE utf8_czech_ci NOT NULL,
  `role` text COLLATE utf8_czech_ci NOT NULL,
  `fullname` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `username`, `password_hash`, `role`, `fullname`) VALUES
(4,	'joe',	'$2y$10$W27OfVuYx/qdL6SI5mAOFOKjiqS3JIYvXqKEkIeMHzzUvDBZOdPFi',	'admin',	'Joe Kolář'),
(5,	'Kathy',	'$2y$10$8oNfu69w8.vvyrFwZL9rYuFxwkygbPmvINuRU5c2FUMhahSiSIlaW',	'moderator',	'Kathy někdo');

-- 2014-11-19 19:26:46
