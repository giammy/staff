<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913064552 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__person AS SELECT id, username, email, personal_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note FROM person');
        $this->addSql('DROP TABLE person');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB NOT NULL COLLATE BINARY, email CLOB NOT NULL COLLATE BINARY, name CLOB NOT NULL COLLATE BINARY, surname CLOB NOT NULL COLLATE BINARY, group_name CLOB DEFAULT NULL COLLATE BINARY, leader_of_group CLOB DEFAULT NULL COLLATE BINARY, qualification CLOB NOT NULL COLLATE BINARY, organization CLOB NOT NULL COLLATE BINARY, total_hours_per_year INTEGER NOT NULL, total_contractual_hours_per_year INTEGER NOT NULL, parttime_percent DOUBLE PRECISION NOT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, version CLOB NOT NULL COLLATE BINARY, note CLOB DEFAULT NULL COLLATE BINARY, secondary_email CLOB DEFAULT NULL, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL, account_contact_person CLOB NOT NULL, account_is_new BOOLEAN NOT NULL, account_start_date DATETIME NOT NULL, account_end_date DATETIME DEFAULT NULL, account_profile VARCHAR(255) NOT NULL, account_email_enabled BOOLEAN NOT NULL, account_windows_enabled BOOLEAN NOT NULL, account_linux_enabled BOOLEAN NOT NULL, account_note CLOB DEFAULT NULL, account_request_done BOOLEAN NOT NULL, account_sipra_done BOOLEAN NOT NULL, office_phone VARCHAR(255) DEFAULT NULL, office_mobile VARCHAR(255) DEFAULT NULL, office_location VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO person (id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note) SELECT id, username, email, personal_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note FROM __temp__person');
        $this->addSql('DROP TABLE __temp__person');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__person AS SELECT id, username, email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note FROM person');
        $this->addSql('DROP TABLE person');
        $this->addSql('CREATE TABLE person (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB NOT NULL, email CLOB NOT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, group_name CLOB DEFAULT NULL, leader_of_group CLOB DEFAULT NULL, qualification CLOB NOT NULL, organization CLOB NOT NULL, total_hours_per_year INTEGER NOT NULL, total_contractual_hours_per_year INTEGER NOT NULL, parttime_percent DOUBLE PRECISION NOT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, version CLOB NOT NULL, note CLOB DEFAULT NULL, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL, personal_email CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO person (id, username, email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note) SELECT id, username, email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note FROM __temp__person');
        $this->addSql('DROP TABLE __temp__person');
    }
}
