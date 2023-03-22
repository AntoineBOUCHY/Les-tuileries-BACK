<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230117170641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_content_picture (page_content_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_A56499E48F409273 (page_content_id), INDEX IDX_A56499E4EE45BDBF (picture_id), PRIMARY KEY(page_content_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_content_picture ADD CONSTRAINT FK_A56499E48F409273 FOREIGN KEY (page_content_id) REFERENCES page_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_content_picture ADD CONSTRAINT FK_A56499E4EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_content_picture DROP FOREIGN KEY FK_A56499E48F409273');
        $this->addSql('ALTER TABLE page_content_picture DROP FOREIGN KEY FK_A56499E4EE45BDBF');
        $this->addSql('DROP TABLE page_content_picture');
    }
}
