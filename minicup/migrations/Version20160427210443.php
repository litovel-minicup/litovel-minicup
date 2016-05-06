<?php

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160427210443 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $matchTerm = $schema->getTable('match_term');
        $matchTerm->getColumn('start')->setType(Type::getType(Type::DATETIME));
        $matchTerm->getColumn('end')->setType(Type::getType(Type::DATETIME));

        $this->addSql("UPDATE match_term SET start = CONCAT('1000-01-01 ', TIME(start)), end = CONCAT('1000-01-01 ', TIME(end));");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $matchTerm = $schema->getTable('match_term');
        $matchTerm->getColumn('start')->setType(Type::getType(Type::TIME));
        $matchTerm->getColumn('end')->setType(Type::getType(Type::TIME));
    }
}
