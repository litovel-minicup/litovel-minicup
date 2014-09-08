/*
Created		5.9.2014
Modified		6.9.2014
Project		
Model		
Company		
Author		
Version		
Database		mySQL 4.1 
*/







drop table IF EXISTS `online_report`;
drop table IF EXISTS `match_term`;
drop table IF EXISTS `category`;
drop table IF EXISTS `team`;
drop table IF EXISTS `match`;




Create table `match` (
	`id` Int NOT NULL AUTO_INCREMENT,
	`match_term_id` Int NOT NULL,
	`category_id` Int NOT NULL,
	`home_team_id` Int NOT NULL,
	`away_team_id` Int NOT NULL,
	`score_home` Int,
	`score_away` Int,
	UNIQUE (`id`),
	Index `AI_id` (`id`),
 Primary Key (`id`)) ENGINE = MyISAM;

Create table `team` (
	`id` Int NOT NULL,
	`name` Char(20) NOT NULL,
	`slug` Char(20) NOT NULL,
 Primary Key (`id`)) ENGINE = MyISAM;

Create table `category` (
	`id` Int NOT NULL,
	`name` Char(20) NOT NULL,
	`slug` Char(20) NOT NULL,
	UNIQUE (`slug`),
 Primary Key (`id`)) ENGINE = MyISAM;

Create table `match_term` (
	`id` Int NOT NULL,
	`start` Datetime NOT NULL,
	`end` Datetime NOT NULL,
 Primary Key (`id`)) ENGINE = MyISAM;

Create table `online_report` (
	`id` Int NOT NULL,
	`match_id` Int NOT NULL,
	`message` Text NOT NULL,
	`type` Char(20) NOT NULL,
	`timestamp` Timestamp NOT NULL,
 Primary Key (`id`)) ENGINE = MyISAM;












Alter table `online_report` add Foreign Key (`match_id`) references `match` (`id`) on delete  restrict on update  restrict;
Alter table `match` add Foreign Key (`home_team_id`) references `team` (`id`) on delete  restrict on update  restrict;
Alter table `match` add Foreign Key (`away_team_id`) references `team` (`id`) on delete  restrict on update  restrict;
Alter table `match` add Foreign Key (`category_id`) references `category` (`id`) on delete  restrict on update  restrict;
Alter table `match` add Foreign Key (`match_term_id`) references `match_term` (`id`) on delete  restrict on update  restrict;



