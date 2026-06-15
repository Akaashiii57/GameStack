<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610114141 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description, mode FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, description CLOB DEFAULT NULL, mode VARCHAR(50) DEFAULT NULL)');
        $this->addSql('INSERT INTO game (id, title, cover, developer, publisher, release_date, estimated_playtime, description, mode) SELECT id, title, cover_url, developer, publisher, release_date, estimated_playtime, description, mode FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__game AS SELECT id, title, cover, developer, publisher, release_date, estimated_playtime, mode, description FROM game');
        $this->addSql('DROP TABLE game');
        $this->addSql('CREATE TABLE game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover_url VARCHAR(255) DEFAULT NULL, developer VARCHAR(255) DEFAULT NULL, publisher VARCHAR(255) DEFAULT NULL, release_date DATE DEFAULT NULL, estimated_playtime INTEGER DEFAULT NULL, mode VARCHAR(50) DEFAULT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO game (id, title, cover_url, developer, publisher, release_date, estimated_playtime, mode, description) SELECT id, title, cover, developer, publisher, release_date, estimated_playtime, mode, description FROM __temp__game');
        $this->addSql('DROP TABLE __temp__game');
    }
}
