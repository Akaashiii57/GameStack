<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260609120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at to game_user';
    }

    public function up(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->addSql('ALTER TABLE game_user ADD COLUMN created_at DATETIME DEFAULT NULL');

            return;
        }

        $this->addSql('ALTER TABLE game_user ADD created_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        if ($this->connection->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->addSql('CREATE TEMPORARY TABLE __temp__game_user AS SELECT id, username, email, roles, password, last_activity_at FROM game_user');
            $this->addSql('DROP TABLE game_user');
            $this->addSql('CREATE TABLE game_user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
, password VARCHAR(255) NOT NULL, last_activity_at DATETIME DEFAULT NULL)');
            $this->addSql('INSERT INTO game_user (id, username, email, roles, password, last_activity_at) SELECT id, username, email, roles, password, last_activity_at FROM __temp__game_user');
            $this->addSql('DROP TABLE __temp__game_user');

            return;
        }

        $this->addSql('ALTER TABLE game_user DROP created_at');
    }
}