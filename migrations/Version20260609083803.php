<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260609083803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add last_activity_at column to game_user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_user ADD COLUMN last_activity_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__game_user AS SELECT id, username, email, roles, password, created_at FROM game_user');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO game_user (id, username, email, roles, password, created_at) SELECT id, username, email, roles, password, created_at FROM __temp__game_user');
        $this->addSql('DROP TABLE __temp__game_user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON game_user (email)');
    }
}
