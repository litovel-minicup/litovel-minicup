<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160502163419 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $news = $schema->getTable('news');
        $news->addColumn('tag_id', Type::INTEGER)->setNotnull(FALSE);
        $news->addForeignKeyConstraint('tag', ['tag_id'], ['id'], [
            'onUpdate' => 'SET NULL',
            'onDelete' => 'SET NULL'
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $news = $schema->getTable('news');
        $news->dropColumn('tag_id');
    }
}
