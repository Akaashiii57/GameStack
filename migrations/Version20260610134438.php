<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610134438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE steam_account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, steam_id VARCHAR(255) NOT NULL, persona_name VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, profile_url VARCHAR(255) DEFAULT NULL, linked_at DATETIME NOT NULL, last_sync_at DATETIME DEFAULT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_F1D0B734A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F1D0B734A76ED395 ON steam_account (user_id)');
        $this->addSql('CREATE TABLE steam_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, app_id INTEGER NOT NULL, name VARCHAR(255) DEFAULT NULL, playtime_forever INTEGER NOT NULL, playtime2_weeks INTEGER NOT NULL, last_played DATETIME DEFAULT NULL, last_sync_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_EA3787B4A76ED395 FOREIGN KEY (user_id) REFERENCES game_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EA3787B4A76ED395 ON steam_game (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE steam_account');
        $this->addSql('DROP TABLE steam_game');
    }
}
