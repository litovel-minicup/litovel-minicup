<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180507163423 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE match_event MODIFY id INT(11) NOT NULL AUTO_INCREMENT;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE match_event MODIFY id INT(11) NOT NULL;');
    }
}
