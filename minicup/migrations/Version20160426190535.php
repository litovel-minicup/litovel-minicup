<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160426190535 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $yearId = reset($this->connection->fetchAll('SELECT * FROM `year` WHERE `year` = \'2015\' LIMIT 1'))['id'];

        $this->addSql(
            'UPDATE tag SET year_id = ? WHERE year_id IS NULL ', [$yearId]
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
