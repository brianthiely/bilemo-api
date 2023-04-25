<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230423154358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD id INT AUTO_INCREMENT NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD github_id VARCHAR(255) DEFAULT NULL, DROP client_id, DROP company, DROP contact_name, DROP contact_email, DROP created_at, CHANGE uuid email VARCHAR(180) NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455E7927C74 ON client (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_C7440455E7927C74 ON client');
        $this->addSql('DROP INDEX `primary` ON client');
        $this->addSql('ALTER TABLE client ADD client_id INT NOT NULL, ADD contact_name VARCHAR(255) NOT NULL, ADD contact_email VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP id, DROP github_id, CHANGE email uuid VARCHAR(180) NOT NULL, CHANGE password company VARCHAR(255) NOT NULL');
    }
}
