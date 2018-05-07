<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180507165059 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE `match_event` CONVERT TO CHARACTER SET 'utf8';");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
