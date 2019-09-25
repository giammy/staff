<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190925152558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL, created DATETIME DEFAULT NULL, requested DATETIME NOT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, contact_person CLOB NOT NULL, account_is_new BOOLEAN NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME DEFAULT NULL, profile VARCHAR(255) DEFAULT NULL, group_name CLOB DEFAULT NULL, email_enabled BOOLEAN NOT NULL, windows_enabled BOOLEAN NOT NULL, linux_enabled BOOLEAN NOT NULL, note CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL, email CLOB DEFAULT NULL, secondary_email CLOB DEFAULT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, group_name CLOB DEFAULT NULL, leader_of_group CLOB DEFAULT NULL, qualification CLOB DEFAULT NULL, organization CLOB DEFAULT NULL, total_hours_per_year INTEGER DEFAULT NULL, total_contractual_hours_per_year INTEGER DEFAULT NULL, parttime_percent DOUBLE PRECISION DEFAULT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL, version CLOB NOT NULL, note CLOB DEFAULT NULL, office_phone VARCHAR(255) DEFAULT NULL, office_mobile VARCHAR(255) DEFAULT NULL, office_location VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE staff');
    }
}
