-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP DATABASE IF EXISTS `minicup-2015`;
CREATE DATABASE `minicup-2015` /*!40100 DEFAULT CHARACTER SET utf8
  COLLATE utf8_czech_ci */;
USE `minicup-2015`;

DROP TABLE IF EXISTS `year`;
CREATE TABLE `year` (
  `id`     INT(11)    NOT NULL AUTO_INCREMENT,
  `year`   YEAR(4)    NOT NULL,
  `name`   TEXT
           COLLATE utf8_czech_ci,
  `actual` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id`            INT(11)               NOT NULL AUTO_INCREMENT,
  `username`      TEXT
                  COLLATE utf8_czech_ci NOT NULL,
  `password_hash` TEXT
                  COLLATE utf8_czech_ci NOT NULL,
  `role`          TEXT
                  COLLATE utf8_czech_ci NOT NULL,
  `fullname`      TEXT
                  COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id`   INT(11)               NOT NULL AUTO_INCREMENT,
  `name` CHAR(30)
         COLLATE utf8_czech_ci NOT NULL,
  `slug` CHAR(30)
         COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `day`;
CREATE TABLE `day` (
  `id`      INT(11) NOT NULL AUTO_INCREMENT,
  `day`     DATE    NOT NULL,
  `year_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `year_id` (`year_id`),
  CONSTRAINT `day_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `match_term`;
CREATE TABLE `match_term` (
  `id`       INT(11)               NOT NULL AUTO_INCREMENT,
  `start`    TIME                  NOT NULL,
  `end`      TIME                  NOT NULL,
  `day_id`   INT(11)               NOT NULL,
  `location` VARCHAR(50)
             COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `day_id` (`day_id`),
  CONSTRAINT `day_id` FOREIGN KEY (`day_id`) REFERENCES `day` (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `team_info`;
CREATE TABLE `team_info` (
  `id`   INT(11)               NOT NULL AUTO_INCREMENT,
  `name` CHAR(30)
         COLLATE utf8_czech_ci NOT NULL,
  `slug` CHAR(30)
         COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id`             INT(11)  NOT NULL AUTO_INCREMENT,
  `team_info_id`   INT(11)  NOT NULL,
  `order`          INT(11)  NOT NULL,
  `category_id`    INT(11)  NOT NULL,
  `actual`         INT(11)  NOT NULL DEFAULT '0',
  `inserted`       DATETIME NOT NULL,
  `after_match_id` INT(11)           DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `team_info_id` (`team_info_id`),
  KEY `after_match_id` (`after_match_id`),
  CONSTRAINT `team_ibfk_1` FOREIGN KEY (`team_info_id`) REFERENCES `team_info` (`id`),
  CONSTRAINT `team_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;

DROP TABLE IF EXISTS `match`;
CREATE TABLE `match` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `match_term_id` INT(11)          DEFAULT NULL,
  `category_id`   INT(11) NOT NULL,
  `home_team_id`  INT(11) NOT NULL,
  `away_team_id`  INT(11) NOT NULL,
  `score_home`    INT(11)          DEFAULT NULL,
  `score_away`    INT(11)          DEFAULT NULL,
  `played`        INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `AI_id` (`id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  KEY `match_term_id` (`match_term_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `match_ibfk_1` FOREIGN KEY (`match_term_id`) REFERENCES `match_term` (`id`),
  CONSTRAINT `match_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  CONSTRAINT `match_ibfk_3` FOREIGN KEY (`home_team_id`) REFERENCES `team` (`id`),
  CONSTRAINT `match_ibfk_4` FOREIGN KEY (`away_team_id`) REFERENCES `team` (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;


DROP TABLE IF EXISTS `online_report`;
CREATE TABLE `online_report` (
  `id`       INT(11)               NOT NULL AUTO_INCREMENT,
  `match_id` INT(11)               NOT NULL,
  `message`  TEXT
             COLLATE utf8_czech_ci NOT NULL,
  `type`     CHAR(20)
             COLLATE utf8_czech_ci NOT NULL,
  `updated`  TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added`    DATETIME              NOT NULL,
  `author`   INT(11)               NOT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `author` (`author`),
  CONSTRAINT `online_report_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match` (`id`),
  CONSTRAINT `online_report_ibfk_3` FOREIGN KEY (`author`) REFERENCES `user` (`id`)
)
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  COLLATE =utf8_czech_ci;

ALTER TABLE `team` ADD FOREIGN KEY (`after_match_id`) REFERENCES `match` (`id`);

-- 2015-01-04 18:19:18
