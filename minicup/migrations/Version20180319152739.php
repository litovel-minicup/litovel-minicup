<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Doctrine\DBAL\Types\Type;
use Minicup\Model\Entity\Match;
use Minicup\Model\Entity\MatchEvent;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180319152739 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $schema->getTable('match')->addColumn('online_state', Type::STRING)->setNotnull(FALSE)->setDefault('init');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->getTable('match')->dropColumn('online_state');

    }
}
