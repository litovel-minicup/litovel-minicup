<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518192438 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team_info ADD COLUMN color_primary TEXT DEFAULT NULL;');
        $this->addSql('ALTER TABLE team_info ADD COLUMN color_secondary TEXT DEFAULT NULL;');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team_info DROP COLUMN color_primary;');
        $this->addSql('ALTER TABLE team_info DROP COLUMN color_secondary;');
    }
}
