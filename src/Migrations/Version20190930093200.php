<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190930093200 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__account AS SELECT id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note FROM account');
        $this->addSql('DROP TABLE account');
        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL COLLATE BINARY, name CLOB NOT NULL COLLATE BINARY, surname CLOB NOT NULL COLLATE BINARY, contact_person CLOB NOT NULL COLLATE BINARY, account_is_new BOOLEAN NOT NULL, profile VARCHAR(255) DEFAULT NULL COLLATE BINARY, group_name CLOB DEFAULT NULL COLLATE BINARY, email_enabled BOOLEAN NOT NULL, windows_enabled BOOLEAN NOT NULL, linux_enabled BOOLEAN NOT NULL, note CLOB DEFAULT NULL COLLATE BINARY, created DATETIME DEFAULT NULL, requested DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME DEFAULT NULL, it_regulation_accepted BOOLEAN NOT NULL, version INTEGER NOT NULL, internal_note VARCHAR(1024) DEFAULT NULL)');
        $this->addSql('INSERT INTO account (id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note) SELECT id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note FROM __temp__account');
        $this->addSql('DROP TABLE __temp__account');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL COLLATE BINARY, email CLOB DEFAULT NULL COLLATE BINARY, secondary_email CLOB DEFAULT NULL COLLATE BINARY, name CLOB NOT NULL COLLATE BINARY, surname CLOB NOT NULL COLLATE BINARY, group_name CLOB DEFAULT NULL COLLATE BINARY, leader_of_group CLOB DEFAULT NULL COLLATE BINARY, qualification CLOB DEFAULT NULL COLLATE BINARY, organization CLOB DEFAULT NULL COLLATE BINARY, total_hours_per_year INTEGER DEFAULT NULL, total_contractual_hours_per_year INTEGER DEFAULT NULL, parttime_percent DOUBLE PRECISION DEFAULT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, version CLOB NOT NULL COLLATE BINARY, note CLOB DEFAULT NULL COLLATE BINARY, office_phone VARCHAR(255) DEFAULT NULL COLLATE BINARY, office_mobile VARCHAR(255) DEFAULT NULL COLLATE BINARY, office_location VARCHAR(255) DEFAULT NULL COLLATE BINARY, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL)');
        $this->addSql('INSERT INTO staff (id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location) SELECT id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__account AS SELECT id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note FROM account');
        $this->addSql('DROP TABLE account');
        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, contact_person CLOB NOT NULL, account_is_new BOOLEAN NOT NULL, profile VARCHAR(255) DEFAULT NULL, group_name CLOB DEFAULT NULL, email_enabled BOOLEAN NOT NULL, windows_enabled BOOLEAN NOT NULL, linux_enabled BOOLEAN NOT NULL, note CLOB DEFAULT NULL, created DATETIME DEFAULT NULL, requested DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO account (id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note) SELECT id, username, created, requested, name, surname, contact_person, account_is_new, valid_from, valid_to, profile, group_name, email_enabled, windows_enabled, linux_enabled, note FROM __temp__account');
        $this->addSql('DROP TABLE __temp__account');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username CLOB DEFAULT NULL, email CLOB DEFAULT NULL, secondary_email CLOB DEFAULT NULL, name CLOB NOT NULL, surname CLOB NOT NULL, group_name CLOB DEFAULT NULL, leader_of_group CLOB DEFAULT NULL, qualification CLOB DEFAULT NULL, organization CLOB DEFAULT NULL, total_hours_per_year INTEGER DEFAULT NULL, total_contractual_hours_per_year INTEGER DEFAULT NULL, parttime_percent DOUBLE PRECISION DEFAULT NULL, is_time_sheet_enabled BOOLEAN NOT NULL, version CLOB NOT NULL, note CLOB DEFAULT NULL, office_phone VARCHAR(255) DEFAULT NULL, office_mobile VARCHAR(255) DEFAULT NULL, office_location VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, valid_from DATETIME NOT NULL, valid_to DATETIME NOT NULL)');
        $this->addSql('INSERT INTO staff (id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location) SELECT id, username, email, secondary_email, name, surname, group_name, leader_of_group, qualification, organization, total_hours_per_year, total_contractual_hours_per_year, parttime_percent, is_time_sheet_enabled, created, valid_from, valid_to, version, note, office_phone, office_mobile, office_location FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
    }
}
