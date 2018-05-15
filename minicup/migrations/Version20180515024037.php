<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180515024037 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `team_info` ADD COLUMN `abbr` VARCHAR(4) DEFAULT NULL;');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `team_info` DROP COLUMN `abbr`;');
    }
}
