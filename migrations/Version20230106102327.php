<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106102327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, src VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_location (image_id INT NOT NULL, location_id VARCHAR(255) NOT NULL, INDEX IDX_D16E07633DA5256D (image_id), INDEX IDX_D16E076364D218E (location_id), PRIMARY KEY(image_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_location ADD CONSTRAINT FK_D16E07633DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_location ADD CONSTRAINT FK_D16E076364D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE location DROP images');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_location DROP FOREIGN KEY FK_D16E07633DA5256D');
        $this->addSql('ALTER TABLE image_location DROP FOREIGN KEY FK_D16E076364D218E');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE image_location');
        $this->addSql('ALTER TABLE location ADD images LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
