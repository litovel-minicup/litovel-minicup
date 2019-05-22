<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190522193656 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE team_info ADD COLUMN color_text TEXT DEFAULT NULL;');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE team_info DROP COLUMN color_text;');
    }
}
