<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180325191404 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $schema->getTable('match_event')->addColumn('team_info_id', Type::INTEGER)->setNotnull(FALSE);
        $schema->getTable('match_event')->addForeignKeyConstraint(
            'team_info',
            ['team_info_id'],
            ['id']
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->getTable('match')->dropColumn('team_info_id');
    }
}
