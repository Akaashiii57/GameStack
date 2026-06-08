<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260605133044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE library_game DROP FOREIGN KEY `FK_85E4FBE14D77E7D8`');
        $this->addSql('ALTER TABLE library_game DROP FOREIGN KEY `FK_85E4FBE19D86650F`');
        $this->addSql('DROP TABLE library_game');
        $this->addSql('DROP TABLE user_id');
        $this->addSql('ALTER TABLE game DROP created_at, CHANGE cover_url cover_url VARCHAR(255) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE developper developer VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE library_game (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, personal_rating SMALLINT DEFAULT NULL, personal_review LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, playtime INT DEFAULT NULL, started_at DATE DEFAULT NULL, finished_at DATE DEFAULT NULL, is_favorite TINYINT NOT NULL, added_at DATE NOT NULL, user_id_id INT NOT NULL, game_id_id INT NOT NULL, INDEX IDX_85E4FBE19D86650F (user_id_id), INDEX IDX_85E4FBE14D77E7D8 (game_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_id (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE library_game ADD CONSTRAINT `FK_85E4FBE14D77E7D8` FOREIGN KEY (game_id_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE library_game ADD CONSTRAINT `FK_85E4FBE19D86650F` FOREIGN KEY (user_id_id) REFERENCES game_user (id)');
        $this->addSql('ALTER TABLE game ADD created_at DATE NOT NULL, CHANGE cover_url cover_url VARCHAR(255) DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE developer developper VARCHAR(255) DEFAULT NULL');
    }
}
