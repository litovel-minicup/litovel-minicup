<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160506220840 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $staticContent = $schema->getTable('static_content');
        $staticContent->addColumn('year_id', Type::INTEGER)->setNotnull(FALSE);
        $staticContent->addForeignKeyConstraint('year', ['year_id'], ['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $staticContent = $schema->getTable('static_content');
        $staticContent->dropColumn('year_id');
    }
}
