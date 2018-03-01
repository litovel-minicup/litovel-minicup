<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Extension for models:
 *
 * -> Creates new Player model
 * -> Update TeamInfo model
 */
class Version20180301223604 extends AbstractMigration
{

    const TEAM_INFO_TABLE = "team_info";

    const PLAYER_TABLE = "player";

    const COLUMNS =  [
        [
            "name" => "dress_color",
            "type" => "char(6)",
            "notnull" => false
        ],
        [
            "name" => "dress_color_secundary",
            "type" => "char(6)",
            "notnull" => true
        ],
        [
            "name" => "trainer_name",
            "type" => "varchar(50)",
            "notnull" => false
        ],
        [
            "name" => "text_info",
            "type" => "text",
            "notnull" => true
        ],
        [
            "name" => "password",
            "type" => "text",
            "notnull" => false
        ],
        [
            "name" => "administration_flag",
            "type" => "BIT(1)",
            "notnull" => false
        ],
        [
            "name" => "auth_token",
            "type" => "text",
            "notnull" => true
        ]

    ];

    /**
     * Creates table for player, update player_info
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // Add table for player model Player
        $this->addSql(

            "CREATE TABLE `".$this::TEAM_INFO_TABLE."` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
                  `surname` varchar(50) COLLATE utf8_czech_ci NOT NULL,
                  `number` int(11) NOT NULL,
                  `secundary_number` int(11) NOT NULL,
                  `team_info_id` int(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `team_info_id` (`team_info_id`),
                  CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team_info_id`) REFERENCES `team_info` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;"
        );

        // Update TeamIfo model
        foreach($this::COLUMNS as $column) {

            $schema->getTable($this::TEAM_INFO_TABLE)
                ->addColumn($column['name'], $column['type'])
                ->setNotnull($column['notnull']);

        }

    }

    /**
     * Drop table for model Player, drop some columns in player_info
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

        $schema->dropTable($this::TEAM_INFO_TABLE);

        // Update TeamIfo model
        foreach($this::COLUMNS as $column) {

            $schema->getTable($this::TEAM_INFO_TABLE)
                ->dropColumn($column['name']);

        }

    }
}
