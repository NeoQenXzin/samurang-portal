<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215182613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dojang_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE formation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE grade_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE instructor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE next_order_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE student_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON admin (email)');
        $this->addSql('CREATE TABLE dojang (id INT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE formation (id INT NOT NULL, organizer_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, location VARCHAR(255) NOT NULL, participants_count INT NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_404021BF876C4DDA ON formation (organizer_id)');
        $this->addSql('CREATE TABLE formation_instructor (formation_id INT NOT NULL, instructor_id INT NOT NULL, PRIMARY KEY(formation_id, instructor_id))');
        $this->addSql('CREATE INDEX IDX_C95490E45200282E ON formation_instructor (formation_id)');
        $this->addSql('CREATE INDEX IDX_C95490E48C4FC193 ON formation_instructor (instructor_id)');
        $this->addSql('CREATE TABLE formation_student (formation_id INT NOT NULL, student_id INT NOT NULL, PRIMARY KEY(formation_id, student_id))');
        $this->addSql('CREATE INDEX IDX_F2DCEA485200282E ON formation_student (formation_id)');
        $this->addSql('CREATE INDEX IDX_F2DCEA48CB944F1A ON formation_student (student_id)');
        $this->addSql('CREATE TABLE grade (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE instructor (id INT NOT NULL, grade_id INT NOT NULL, dojang_id INT DEFAULT NULL, firstname VARCHAR(70) NOT NULL, lastname VARCHAR(70) NOT NULL, birthdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, adress VARCHAR(255) NOT NULL, sexe VARCHAR(25) NOT NULL, tel VARCHAR(25) DEFAULT NULL, mail VARCHAR(70) NOT NULL, passport VARCHAR(25) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, reset_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_31FC43DDFE19A1A8 ON instructor (grade_id)');
        $this->addSql('CREATE INDEX IDX_31FC43DD1BD0A3F1 ON instructor (dojang_id)');
        $this->addSql('CREATE TABLE next_order (id INT NOT NULL, send_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, receive_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status VARCHAR(25) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE student (id INT NOT NULL, grade_id INT NOT NULL, dojang_id INT DEFAULT NULL, instructor_id INT DEFAULT NULL, firstname VARCHAR(70) NOT NULL, lastname VARCHAR(70) NOT NULL, birthdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, adress VARCHAR(255) NOT NULL, sexe VARCHAR(25) NOT NULL, tel VARCHAR(25) DEFAULT NULL, mail VARCHAR(70) NOT NULL, passport VARCHAR(25) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, reset_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B723AF33FE19A1A8 ON student (grade_id)');
        $this->addSql('CREATE INDEX IDX_B723AF331BD0A3F1 ON student (dojang_id)');
        $this->addSql('CREATE INDEX IDX_B723AF338C4FC193 ON student (instructor_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF876C4DDA FOREIGN KEY (organizer_id) REFERENCES instructor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_instructor ADD CONSTRAINT FK_C95490E45200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_instructor ADD CONSTRAINT FK_C95490E48C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA485200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA48CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instructor ADD CONSTRAINT FK_31FC43DDFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instructor ADD CONSTRAINT FK_31FC43DD1BD0A3F1 FOREIGN KEY (dojang_id) REFERENCES dojang (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF331BD0A3F1 FOREIGN KEY (dojang_id) REFERENCES dojang (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF338C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE admin_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dojang_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE formation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE grade_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE instructor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE next_order_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE student_id_seq CASCADE');
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BF876C4DDA');
        $this->addSql('ALTER TABLE formation_instructor DROP CONSTRAINT FK_C95490E45200282E');
        $this->addSql('ALTER TABLE formation_instructor DROP CONSTRAINT FK_C95490E48C4FC193');
        $this->addSql('ALTER TABLE formation_student DROP CONSTRAINT FK_F2DCEA485200282E');
        $this->addSql('ALTER TABLE formation_student DROP CONSTRAINT FK_F2DCEA48CB944F1A');
        $this->addSql('ALTER TABLE instructor DROP CONSTRAINT FK_31FC43DDFE19A1A8');
        $this->addSql('ALTER TABLE instructor DROP CONSTRAINT FK_31FC43DD1BD0A3F1');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF33FE19A1A8');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF331BD0A3F1');
        $this->addSql('ALTER TABLE student DROP CONSTRAINT FK_B723AF338C4FC193');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE dojang');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_instructor');
        $this->addSql('DROP TABLE formation_student');
        $this->addSql('DROP TABLE grade');
        $this->addSql('DROP TABLE instructor');
        $this->addSql('DROP TABLE next_order');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
