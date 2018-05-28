<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180520121239 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE match_term SET end = start + INTERVAL 30 MINUTE where end is null;');
        $this->addSql("
        UPDATE match_term
        SET start = start + INTERVAL 2000 YEAR
        WHERE cast(start AS DATE) = '0001-01-01';
        ");
        $this->addSql("
        UPDATE match_term
        SET end = end + INTERVAL 2000 YEAR
        WHERE cast(end AS DATE) = '0001-01-01';
        ");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
