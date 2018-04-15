<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312231152 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(<<<SQL
        DROP TABLE IF EXISTS `online_report`;
SQL
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql(<<<SQL
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_report` (
  `id`       INT(11)                        NOT NULL AUTO_INCREMENT,
  `match_id` INT(11)                        NOT NULL,
  `message`  TEXT COLLATE utf8_czech_ci     NOT NULL,
  `type`     CHAR(20) COLLATE utf8_czech_ci NOT NULL,
  `updated`  TIMESTAMP                      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `added`    DATETIME                       NOT NULL,
  `author`   INT(11)                        NOT NULL,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `author` (`author`),
  CONSTRAINT `online_report_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `match` (`id`),
  CONSTRAINT `online_report_ibfk_3` FOREIGN KEY (`author`) REFERENCES `user` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_czech_ci;
SQL
        );

    }
}
