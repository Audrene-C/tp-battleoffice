<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200729082139 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing CHANGE adress_line1 address_line1 VARCHAR(255) NOT NULL, CHANGE adress_line2 address_line2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939885E14726');
        $this->addSql('DROP INDEX UNIQ_F529939885E14726 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE adresses_id addresses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993985713BC80 FOREIGN KEY (addresses_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52993985713BC80 ON `order` (addresses_id)');
        $this->addSql('ALTER TABLE shipping ADD address_line1 VARCHAR(255) DEFAULT NULL, ADD address_line2 VARCHAR(255) DEFAULT NULL, DROP adress_line1, DROP adress_line2');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing CHANGE address_line1 adress_line1 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE address_line2 adress_line2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993985713BC80');
        $this->addSql('DROP INDEX UNIQ_F52993985713BC80 ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE addresses_id adresses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939885E14726 FOREIGN KEY (adresses_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F529939885E14726 ON `order` (adresses_id)');
        $this->addSql('ALTER TABLE shipping ADD adress_line1 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD adress_line2 VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP address_line1, DROP address_line2');
    }
}
