<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424104957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "ncp_list" (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, state VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, owner_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_CEF049477E3C61F9 FOREIGN KEY (owner_id) REFERENCES "ncp_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CEF049477E3C61F9 ON "ncp_list" (owner_id)');
        $this->addSql('CREATE TABLE "ncp_list_item" (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, state VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, list_id CHAR(36) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_AE4C4FFB3DAE168B FOREIGN KEY (list_id) REFERENCES "ncp_list" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AE4C4FFB3DAE168B ON "ncp_list_item" (list_id)');
        $this->addSql('CREATE TABLE "ncp_user" (id CHAR(36) NOT NULL, name VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AB67165E237E06 ON "ncp_user" (name)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "ncp_list"');
        $this->addSql('DROP TABLE "ncp_list_item"');
        $this->addSql('DROP TABLE "ncp_user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
