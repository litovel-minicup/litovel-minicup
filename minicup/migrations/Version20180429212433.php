<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180429212433 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        UPDATE match_term
SET start = start + INTERVAL 2000 YEAR
WHERE cast(start AS DATE) = '0001-01-01';
");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
