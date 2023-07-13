<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713150148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE degree_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE opinion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE professor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subject_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE university_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE degree (id INT NOT NULL, university_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A7A36D63309D1878 ON degree (university_id)');
        $this->addSql('CREATE TABLE opinion (id INT NOT NULL, subject_id INT DEFAULT NULL, professor_id INT DEFAULT NULL, owner_id INT NOT NULL, comment TEXT DEFAULT NULL, score JSON DEFAULT NULL, keywords JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AB02B02723EDC87 ON opinion (subject_id)');
        $this->addSql('CREATE INDEX IDX_AB02B0277D2D84D5 ON opinion (professor_id)');
        $this->addSql('CREATE INDEX IDX_AB02B0277E3C61F9 ON opinion (owner_id)');
        $this->addSql('CREATE TABLE professor (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE professor_subject (professor_id INT NOT NULL, subject_id INT NOT NULL, PRIMARY KEY(professor_id, subject_id))');
        $this->addSql('CREATE INDEX IDX_A4E1512E7D2D84D5 ON professor_subject (professor_id)');
        $this->addSql('CREATE INDEX IDX_A4E1512E23EDC87 ON professor_subject (subject_id)');
        $this->addSql('CREATE TABLE subject (id INT NOT NULL, degree_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FBCE3E7AB35C5756 ON subject (degree_id)');
        $this->addSql('CREATE TABLE university (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
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
        $this->addSql('ALTER TABLE degree ADD CONSTRAINT FK_A7A36D63309D1878 FOREIGN KEY (university_id) REFERENCES university (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B02723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B0277D2D84D5 FOREIGN KEY (professor_id) REFERENCES professor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B0277E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE professor_subject ADD CONSTRAINT FK_A4E1512E7D2D84D5 FOREIGN KEY (professor_id) REFERENCES professor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE professor_subject ADD CONSTRAINT FK_A4E1512E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AB35C5756 FOREIGN KEY (degree_id) REFERENCES degree (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE degree_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE opinion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE professor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subject_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE university_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE degree DROP CONSTRAINT FK_A7A36D63309D1878');
        $this->addSql('ALTER TABLE opinion DROP CONSTRAINT FK_AB02B02723EDC87');
        $this->addSql('ALTER TABLE opinion DROP CONSTRAINT FK_AB02B0277D2D84D5');
        $this->addSql('ALTER TABLE opinion DROP CONSTRAINT FK_AB02B0277E3C61F9');
        $this->addSql('ALTER TABLE professor_subject DROP CONSTRAINT FK_A4E1512E7D2D84D5');
        $this->addSql('ALTER TABLE professor_subject DROP CONSTRAINT FK_A4E1512E23EDC87');
        $this->addSql('ALTER TABLE subject DROP CONSTRAINT FK_FBCE3E7AB35C5756');
        $this->addSql('DROP TABLE degree');
        $this->addSql('DROP TABLE opinion');
        $this->addSql('DROP TABLE professor');
        $this->addSql('DROP TABLE professor_subject');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
