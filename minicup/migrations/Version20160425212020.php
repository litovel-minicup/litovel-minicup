<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160425212020 extends AbstractMigration
{
    /** @var \Doctrine\DBAL\Driver\Connection @inject */
    public $conn;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = file_get_contents(__DIR__ . '/../../utils/minicup_migrations-init.sql');
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $this->addSql('CREATE TRIGGER `bu_match` BEFORE UPDATE ON `match`
FOR EACH ROW BEGIN
  IF (OLD.confirmed IS NULL AND NEW.confirmed IS NOT NULL)
  THEN
    SET NEW.confirmed_as = (SELECT MAX(confirmed_as)
                            FROM `match` m
                            WHERE m.category_id = NEW.category_id) + 1;
  END IF;
END');

        $this->addSql('CREATE TRIGGER `team_bi` BEFORE INSERT ON `team` FOR EACH ROW SET NEW.inserted = NOW()');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException('First PHP migration, cannot be reverted.');
    }
}
