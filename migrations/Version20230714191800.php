<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230714191800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(180)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE "user" DROP roles');
        $this->addSql('ALTER TABLE "user" DROP password');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(255)');
    }
}
