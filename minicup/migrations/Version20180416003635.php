<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180416003635 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE `match` ADD COLUMN  first_half_start DATETIME DEFAULT NULL;
            ALTER TABLE `match` ADD COLUMN second_half_start DATETIME DEFAULT NULL;
    ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE `match` DROP COLUMN  first_half_start;
            ALTER TABLE `match` DROP COLUMN second_half_start;
    ');

    }
}
