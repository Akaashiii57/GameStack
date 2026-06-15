<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610132030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mode');
        $this->addSql('DROP TABLE status');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, description CLOB DEFAULT NULL, mode VARCHAR(50) DEFAULT NULL)');
        $this->addSql('INSERT INTO game (id, title, cover, developer, publisher, release_date, estimated_playtime, description) SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('ALTER TABLE game_user ADD COLUMN last_activity_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__library_game AS SELECT id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id FROM library_game');
        $this->addSql('DROP TABLE library_game');
        $this->addSql('CREATE TABLE library_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, personal_rating SMALLINT DEFAULT NULL, personal_review CLOB DEFAULT NULL, playtime INTEGER DEFAULT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, is_favorite BOOLEAN NOT NULL, added_at DATE NOT NULL, user_id INTEGER NOT NULL, game_id INTEGER NOT NULL, status VARCHAR(50) NOT NULL, CONSTRAINT FK_85E4FBE1A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO library_game (id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id) SELECT id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id FROM __temp__library_game');
        $this->addSql('DROP TABLE __temp__library_game');
        $this->addSql('CREATE INDEX IDX_85E4FBE1E48FD905 ON library_game (game_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1A76ED395 ON library_game (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mode (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE "BINARY")');
        $this->addSql('CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE "BINARY")');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover, developer, publisher, release_date, estimated_playtime, description FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover_url VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime DATE NOT NULL, description CLOB DEFAULT NULL, mode_id INTEGER NOT NULL, CONSTRAINT FK_232B318C77E5854A FOREIGN KEY (mode_id) REFERENCES mode (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO game (id, title, cover_url, developer, publisher, release_date, estimated_playtime, description) SELECT id, title, cover, developer, publisher, release_date, estimated_playtime, description FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
        $this->addSql('CREATE INDEX IDX_232B318C77E5854A ON game (mode_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_user AS SELECT id, username, email, roles, password FROM game_user');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO game_user (id, username, email, roles, password) SELECT id, username, email, roles, password FROM __temp__game_user');
        $this->addSql('DROP TABLE __temp__game_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON game_user (email)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__library_game AS SELECT id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id FROM library_game');
        $this->addSql('DROP TABLE library_game');
        $this->addSql('CREATE TABLE library_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, personal_rating SMALLINT DEFAULT NULL, personal_review CLOB DEFAULT NULL, playtime INTEGER DEFAULT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, is_favorite BOOLEAN NOT NULL, added_at DATE NOT NULL, user_id INTEGER NOT NULL, game_id INTEGER NOT NULL, status_id INTEGER NOT NULL, CONSTRAINT FK_85E4FBE1A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE16BF700BD FOREIGN KEY (status_id) REFERENCES status (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO library_game (id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id) SELECT id, personal_rating, personal_review, playtime, started_at, finished_at, is_favorite, added_at, user_id, game_id FROM __temp__library_game');
        $this->addSql('DROP TABLE __temp__library_game');
        $this->addSql('CREATE INDEX IDX_85E4FBE1A76ED395 ON library_game (user_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1E48FD905 ON library_game (game_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE16BF700BD ON library_game (status_id)');
    }
}
