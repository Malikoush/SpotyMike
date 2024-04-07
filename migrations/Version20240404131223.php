<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404131223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label_artist (label_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_E673A53633B92F39 (label_id), INDEX IDX_E673A536B7970CF8 (artist_id), PRIMARY KEY(label_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_playlist (user_id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_370FF52DA76ED395 (user_id), INDEX IDX_370FF52D6BBD148 (playlist_id), PRIMARY KEY(user_id, playlist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_artist (user_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_640B8DBAA76ED395 (user_id), INDEX IDX_640B8DBAB7970CF8 (artist_id), PRIMARY KEY(user_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE label_artist ADD CONSTRAINT FK_E673A53633B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE label_artist ADD CONSTRAINT FK_E673A536B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_playlist ADD CONSTRAINT FK_370FF52DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_playlist ADD CONSTRAINT FK_370FF52D6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE label_artist DROP FOREIGN KEY FK_E673A53633B92F39');
        $this->addSql('ALTER TABLE label_artist DROP FOREIGN KEY FK_E673A536B7970CF8');
        $this->addSql('ALTER TABLE user_playlist DROP FOREIGN KEY FK_370FF52DA76ED395');
        $this->addSql('ALTER TABLE user_playlist DROP FOREIGN KEY FK_370FF52D6BBD148');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAA76ED395');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAB7970CF8');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE label_artist');
        $this->addSql('DROP TABLE user_playlist');
        $this->addSql('DROP TABLE user_artist');
    }
}
