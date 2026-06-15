<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610074553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mode');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover_url VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, description CLOB DEFAULT NULL, mode VARCHAR(50) DEFAULT NULL)');
        $this->addSql('INSERT INTO game (id, title, cover_url, developer, publisher, release_date, estimated_playtime, description) SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE "BINARY")');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover_url VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime DATE NOT NULL, description CLOB DEFAULT NULL, mode_id INTEGER NOT NULL, CONSTRAINT FK_232B318C77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, title, cover_url, developer, publisher, release_date, estimated_playtime, description) SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C77E5854A ON game (mode_id)');
    }
}
