<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230117144115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(10) NOT NULL, flag VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, is_home_event TINYINT(1) NOT NULL, slider_position SMALLINT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_content (id INT AUTO_INCREMENT NOT NULL, page_id INT NOT NULL, language_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, page_order SMALLINT NOT NULL, INDEX IDX_4A5DB3CC4663E4 (page_id), INDEX IDX_4A5DB3C82F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, news_id INT DEFAULT NULL, path VARCHAR(255) NOT NULL, INDEX IDX_16DB4F89B5A459A0 (news_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture_page_content (picture_id INT NOT NULL, page_content_id INT NOT NULL, INDEX IDX_3D3820BBEE45BDBF (picture_id), INDEX IDX_3D3820BB8F409273 (page_content_id), PRIMARY KEY(picture_id, page_content_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_content ADD CONSTRAINT FK_4A5DB3CC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE page_content ADD CONSTRAINT FK_4A5DB3C82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
        $this->addSql('ALTER TABLE picture_page_content ADD CONSTRAINT FK_3D3820BBEE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture_page_content ADD CONSTRAINT FK_3D3820BB8F409273 FOREIGN KEY (page_content_id) REFERENCES page_content (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_content DROP FOREIGN KEY FK_4A5DB3CC4663E4');
        $this->addSql('ALTER TABLE page_content DROP FOREIGN KEY FK_4A5DB3C82F1BAF4');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B5A459A0');
        $this->addSql('ALTER TABLE picture_page_content DROP FOREIGN KEY FK_3D3820BBEE45BDBF');
        $this->addSql('ALTER TABLE picture_page_content DROP FOREIGN KEY FK_3D3820BB8F409273');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_content');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE picture_page_content');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
