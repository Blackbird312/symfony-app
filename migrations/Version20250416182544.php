<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416182544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(25) NOT NULL, model VARCHAR(10) NOT NULL, year INT NOT NULL, car_license VARCHAR(25) NOT NULL, available TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(50) NOT NULL, driver_license VARCHAR(20) NOT NULL, cin VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, payment_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, payment_id INT NOT NULL, car_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, cost DOUBLE PRECISION NOT NULL, INDEX IDX_1619C27D9395C3F3 (customer_id), UNIQUE INDEX UNIQ_1619C27D4C3A3BB (payment_id), INDEX IDX_1619C27DC3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT FK_1619C27D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT FK_1619C27D4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental ADD CONSTRAINT FK_1619C27DC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D9395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D4C3A3BB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rental DROP FOREIGN KEY FK_1619C27DC3C6F69F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE car
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rental
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
