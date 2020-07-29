<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728143010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD adresses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939885E14726 FOREIGN KEY (adresses_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F529939885E14726 ON `order` (adresses_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939885E14726');
        $this->addSql('DROP INDEX UNIQ_F529939885E14726 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP adresses_id');
    }
}