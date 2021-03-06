-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vygenerováno: Úte 09. zář 2014, 22:25
-- Verze serveru: 5.5.32
-- Verze PHP: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `minicup-2015`
--
CREATE DATABASE IF NOT EXISTS `minicup-2015` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `minicup-2015`;

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`, `slug`) VALUES
(1, 'Mladší', 'mladsi');

-- --------------------------------------------------------

--
-- Struktura tabulky `match`
--

CREATE TABLE IF NOT EXISTS `match` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_term_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `score_home` int(11) DEFAULT NULL,
  `score_away` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `AI_id` (`id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  KEY `category_id` (`category_id`),
  KEY `match_term_id` (`match_term_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `match`
--

INSERT INTO `match` (`id`, `match_term_id`, `category_id`, `home_team_id`, `away_team_id`, `score_home`, `score_away`) VALUES
(2, NULL, 1, 2, 1, 10, 16),
(3, NULL, 1, 3, 1, NULL, NULL),
(4, NULL, 1, 4, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `match_term`
--

CREATE TABLE IF NOT EXISTS `match_term` (
  `id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `online_report`
--

CREATE TABLE IF NOT EXISTS `online_report` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `type` char(20) COLLATE utf8_czech_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(20) COLLATE utf8_czech_ci NOT NULL,
  `slug` char(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `team`
--

INSERT INTO `team` (`id`, `name`, `slug`) VALUES
(1, 'Foo foos', 'foo-foos'),
(2, 'Bar bars', 'bar-bars'),
(3, 'Foo bar', 'foo-bar'),
(4, 'Bar foo', 'bar-foo');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
