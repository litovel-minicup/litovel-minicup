<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180313150829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(<<<SQL
        CREATE TABLE match_event
(
    id INT(11) PRIMARY KEY NOT NULL,
    match_id INT(11) NOT NULL,
    score_home INT,
    score_away INT,
    message TEXT,
    type VARCHAR(16),
    half_index INT NOT NULL,
    time_offset INT NOT NULL,
    player_id INT UNSIGNED,
    CONSTRAINT match_event_match_id_fk FOREIGN KEY (match_id) REFERENCES `match` (id),
    CONSTRAINT match_event_player_id_fk FOREIGN KEY (player_id) REFERENCES player (id)
)
SQL
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE match_event;");

    }
}
