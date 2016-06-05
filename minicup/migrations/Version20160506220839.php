<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160506220839 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $staticContent = $schema->getTable('static_content');

        $staticContent->getColumn('updated')->setNotnull(FALSE)->setDefault(NULL);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
