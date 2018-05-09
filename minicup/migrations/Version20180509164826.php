<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180509164826 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `team_info` ADD COLUMN dress_color_min VARCHAR(7) DEFAULT NULL;');
        $this->addSql('ALTER TABLE `team_info` ADD COLUMN dress_color_max VARCHAR(7) DEFAULT NULL;');
        $this->addSql('ALTER TABLE `team_info` ADD COLUMN dress_color_secondary_min VARCHAR(7) DEFAULT NULL;');
        $this->addSql('ALTER TABLE `team_info` ADD COLUMN dress_color_secondary_max VARCHAR(7) DEFAULT NULL;');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `team_info` DROP COLUMN dress_color_min;');
        $this->addSql('ALTER TABLE `team_info` DROP COLUMN dress_color_max;');
        $this->addSql('ALTER TABLE `team_info` DROP COLUMN dress_color_secondary_min;');
        $this->addSql('ALTER TABLE `team_info` DROP COLUMN dress_color_secondary_max;');

    }
}
