<?php declare(strict_types=1);

namespace Minicup\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190508093212 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
            SET foreign_key_checks = 0; ALTER TABLE category CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE day CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE db_migrations CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE match CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE match_event CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE match_term CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE news CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE online_report CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE photo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE photo_tag CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE player CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE static_content CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE tag CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE team CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE team_info CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE user CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1; 
            SET foreign_key_checks = 0; ALTER TABLE year CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci; SET foreign_key_checks = 1;
SQL
);

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
