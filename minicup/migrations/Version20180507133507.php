<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180507133507 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE news ADD published INT DEFAULT 1 NULL;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE news DROP COLUMN published;');

    }
}
