<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609083106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover_url VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, description CLOB DEFAULT NULL, mode_id INTEGER NOT NULL, CONSTRAINT FK_232B318C77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_232B318C77E5854A ON game (mode_id)');
        $this->addSql('CREATE TABLE library_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, personal_rating SMALLINT DEFAULT NULL, personal_review CLOB DEFAULT NULL, playtime INTEGER DEFAULT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, is_favorite BOOLEAN NOT NULL, added_at DATE NOT NULL, user_id INTEGER NOT NULL, game_id INTEGER NOT NULL, status_id INTEGER NOT NULL, CONSTRAINT FK_85E4FBE1A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE16BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1A76ED395 ON library_game (user_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1E48FD905 ON library_game (game_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE16BF700BD ON library_game (status_id)');
        $this->addSql('CREATE TABLE mode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_user AS SELECT id, username, email, roles, password FROM game_user');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, last_activity_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO game_user (id, username, email, roles, password) SELECT id, username, email, roles, password FROM __temp__game_user');
        $this->addSql('DROP TABLE __temp__game_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON game_user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE library_game');
        $this->addSql('DROP TABLE mode');
        $this->addSql('DROP TABLE status');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_user AS SELECT id, username, email, roles, password FROM game_user');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO game_user (id, username, email, roles, password) SELECT id, username, email, roles, password FROM __temp__game_user');
        $this->addSql('DROP TABLE __temp__game_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON game_user (email)');
    }
}
