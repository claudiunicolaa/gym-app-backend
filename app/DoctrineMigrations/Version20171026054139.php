<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171026054139 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE courses (id INT AUTO_INCREMENT NOT NULL, trainer_id INT DEFAULT NULL, event_date DATETIME NOT NULL, capacity INT NOT NULL, INDEX IDX_A9A55A4CFB08EDF6 (trainer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_courses (user_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_59A52E86A76ED395 (user_id), INDEX IDX_59A52E86591CC992 (course_id), PRIMARY KEY(user_id, course_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CFB08EDF6 FOREIGN KEY (trainer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE users_courses ADD CONSTRAINT FK_59A52E86A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_courses ADD CONSTRAINT FK_59A52E86591CC992 FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(255) DEFAULT NULL, ADD picture VARCHAR(255) DEFAULT NULL, CHANGE fullname first_name VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users_courses DROP FOREIGN KEY FK_59A52E86591CC992');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE users_courses');
        $this->addSql('DROP TABLE products');
        $this->addSql('ALTER TABLE user ADD fullname VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP first_name, DROP last_name, DROP picture');
    }
}
