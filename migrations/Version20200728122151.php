<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728122151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE addresses (id INT AUTO_INCREMENT NOT NULL, billing_id INT NOT NULL, shipping_id INT NOT NULL, UNIQUE INDEX UNIQ_6FCA75163B025C87 (billing_id), UNIQUE INDEX UNIQ_6FCA75164887F3F8 (shipping_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA75163B025C87 FOREIGN KEY (billing_id) REFERENCES billing (id)');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA75164887F3F8 FOREIGN KEY (shipping_id) REFERENCES shipping (id)');
        $this->addSql('DROP TABLE adresses');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresses (id INT AUTO_INCREMENT NOT NULL, billing_id INT NOT NULL, shipping_id INT NOT NULL, UNIQUE INDEX UNIQ_EF1925524887F3F8 (shipping_id), UNIQUE INDEX UNIQ_EF1925523B025C87 (billing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE adresses ADD CONSTRAINT FK_EF1925523B025C87 FOREIGN KEY (billing_id) REFERENCES billing (id)');
        $this->addSql('ALTER TABLE adresses ADD CONSTRAINT FK_EF1925524887F3F8 FOREIGN KEY (shipping_id) REFERENCES shipping (id)');
        $this->addSql('DROP TABLE addresses');
    }
}
