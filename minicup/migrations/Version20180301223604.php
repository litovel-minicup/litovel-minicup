<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Extension for models:
 *
 * -> Creates new Player model
 * -> Update TeamInfo model
 */
class Version20180301223604 extends AbstractMigration
{

    const TABLE_TEAM_INFO = "team_info";

    const TABLE_PLAYER = "player";

    const TABLE_TEAM_INFO_COLUMNS = [
        [
            "name" => "dress_color",
            "type" => Type::STRING,
            "length" => 6,
            "notnull" => false
        ],
        [
            "name" => "dress_color_secondary",
            "type" => Type::STRING,
            "length" => 6,
            "notnull" => true
        ],
        [
            "name" => "trainer_name",
            "type" => Type::STRING,
            "length" => 50,
            "notnull" => false
        ],
        [
            "name" => "description",
            "type" => Type::TEXT,
            "notnull" => true
        ],
        [
            "name" => "password",
            "type" => Type::TEXT,
            "notnull" => false
        ],
        [
            "name" => "updated",
            "type" => Type::DATETIME,
            "notnull" => false
        ],
        [
            "name" => "auth_token",
            "type" => Type::STRING,
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

            "CREATE TABLE `" . $this::TABLE_PLAYER . "` (
                  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(50) COLLATE utf8_czech_ci NOT NULL,
                  `surname` VARCHAR(50) COLLATE utf8_czech_ci NOT NULL,
                  `number` INT(11) NOT NULL,
                  `secondary_number` INT(11),
                  `team_info_id` INT(11) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `team_info_id` (`team_info_id`),
                  CONSTRAINT `player_ibfk_1` FOREIGN KEY (`team_info_id`) REFERENCES `team_info` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;"
        );

        // Update TeamIfo model
        foreach ($this::TABLE_TEAM_INFO_COLUMNS as $column) {

            if (!isset($column['length'])) {
                $schema->getTable($this::TABLE_TEAM_INFO)
                    ->addColumn($column['name'], $column['type'])
                    ->setNotnull($column['notnull']);
            } else {
                $schema->getTable($this::TABLE_TEAM_INFO)
                    ->addColumn($column['name'], $column['type'])
                    ->setLength($column['length'])
                    ->setNotnull($column['notnull']);
            }

        }

    }

    /**
     * Drop table for model Player, drop some columns in player_info
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

        $schema->dropTable($this::TABLE_PLAYER);

        // Update TeamIfo model
        foreach ($this::TABLE_TEAM_INFO_COLUMNS as $column) {

            $schema->getTable($this::TABLE_TEAM_INFO)
                ->dropColumn($column['name']);

        }

    }
}
