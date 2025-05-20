<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250520101000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE car_image (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, file_name VARCHAR(255) NOT NULL, INDEX IDX_1A968188C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE car_image ADD CONSTRAINT FK_1A968188C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE car_image DROP FOREIGN KEY FK_1A968188C3C6F69F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE car_image
        SQL);
    }
}
