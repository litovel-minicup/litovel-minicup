<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190529215408 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `match` ADD COLUMN youtube_video_id TEXT DEFAULT NULL;');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `match` DROP COLUMN youtube_video_id;');

    }
}
