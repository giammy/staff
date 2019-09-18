<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190918093035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB NOT NULL, email CLOB NOT NULL, secondary_email CLOB DEFAULT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, group_name CLOB DEFAULT NULL, leader_of_group CLOB DEFAULT NULL, qualification CLOB NOT NULL, organization CLOB NOT NULL, total_hours_per_year INTEGER NOT NULL, total_contractual_hours_per_year INTEGER NOT NULL, parttime_percent DOUBLE PRECISION NOT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL, version CLOB NOT NULL, note CLOB DEFAULT NULL, account_contact_person CLOB NOT NULL, account_is_new BOOLEAN NOT NULL, account_start_date DATETIME NOT NULL, account_end_date DATETIME DEFAULT NULL, account_profile VARCHAR(255) NOT NULL, account_email_enabled BOOLEAN NOT NULL, account_windows_enabled BOOLEAN NOT NULL, account_linux_enabled BOOLEAN NOT NULL, account_note CLOB DEFAULT NULL, account_request_done BOOLEAN NOT NULL, account_sipra_done BOOLEAN NOT NULL, office_phone VARCHAR(255) DEFAULT NULL, office_mobile VARCHAR(255) DEFAULT NULL, office_location VARCHAR(255) DEFAULT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE staff');
    }
}
