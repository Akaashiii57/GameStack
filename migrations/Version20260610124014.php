<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610124014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, mode VARCHAR(50) DEFAULT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, last_activity_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON game_user (email)');
        $this->addSql('CREATE TABLE library_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status VARCHAR(50) NOT NULL, personal_rating SMALLINT DEFAULT NULL, personal_review CLOB DEFAULT NULL, playtime INTEGER DEFAULT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, is_favorite BOOLEAN NOT NULL, added_at DATE NOT NULL, user_id INTEGER NOT NULL, game_id INTEGER NOT NULL, CONSTRAINT FK_85E4FBE1A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_85E4FBE1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1A76ED395 ON library_game (user_id)');
        $this->addSql('CREATE INDEX IDX_85E4FBE1E48FD905 ON library_game (game_id)');
        $this->addSql('CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE library_game');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
