<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106142347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE instructor ADD reset_token VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE instructor ADD reset_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD reset_token VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE student ADD reset_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE instructor DROP reset_token');
        $this->addSql('ALTER TABLE instructor DROP reset_token_expires_at');
        $this->addSql('ALTER TABLE student DROP reset_token');
        $this->addSql('ALTER TABLE student DROP reset_token_expires_at');
    }
}
