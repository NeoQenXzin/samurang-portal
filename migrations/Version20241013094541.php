<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013094541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE formation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE formation (id INT NOT NULL, organizer_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, location VARCHAR(255) NOT NULL, participants_count INT NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_404021BF876C4DDA ON formation (organizer_id)');
        $this->addSql('CREATE TABLE formation_instructor (formation_id INT NOT NULL, instructor_id INT NOT NULL, PRIMARY KEY(formation_id, instructor_id))');
        $this->addSql('CREATE INDEX IDX_C95490E45200282E ON formation_instructor (formation_id)');
        $this->addSql('CREATE INDEX IDX_C95490E48C4FC193 ON formation_instructor (instructor_id)');
        $this->addSql('CREATE TABLE formation_student (formation_id INT NOT NULL, student_id INT NOT NULL, PRIMARY KEY(formation_id, student_id))');
        $this->addSql('CREATE INDEX IDX_F2DCEA485200282E ON formation_student (formation_id)');
        $this->addSql('CREATE INDEX IDX_F2DCEA48CB944F1A ON formation_student (student_id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF876C4DDA FOREIGN KEY (organizer_id) REFERENCES instructor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_instructor ADD CONSTRAINT FK_C95490E45200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_instructor ADD CONSTRAINT FK_C95490E48C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA485200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA48CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE formation_id_seq CASCADE');
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BF876C4DDA');
        $this->addSql('ALTER TABLE formation_instructor DROP CONSTRAINT FK_C95490E45200282E');
        $this->addSql('ALTER TABLE formation_instructor DROP CONSTRAINT FK_C95490E48C4FC193');
        $this->addSql('ALTER TABLE formation_student DROP CONSTRAINT FK_F2DCEA485200282E');
        $this->addSql('ALTER TABLE formation_student DROP CONSTRAINT FK_F2DCEA48CB944F1A');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_instructor');
        $this->addSql('DROP TABLE formation_student');
    }
}
