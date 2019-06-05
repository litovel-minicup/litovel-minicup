<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190604222856 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER table team_info add column dress_color_histogram TEXT DEFAULT NULL;');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER table team_info drop column dress_color_histogram;');

    }
}
