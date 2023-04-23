<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230423120845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD client_id INT AUTO_INCREMENT NOT NULL, ADD company VARCHAR(255) NOT NULL, ADD contact_name VARCHAR(255) NOT NULL, ADD contact_email VARCHAR(255) NOT NULL, ADD contact_phone VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD auth_token VARCHAR(255) NOT NULL, ADD access_token VARCHAR(255) NOT NULL, DROP id, ADD PRIMARY KEY (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client MODIFY client_id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON client');
        $this->addSql('ALTER TABLE client ADD id INT NOT NULL, DROP client_id, DROP company, DROP contact_name, DROP contact_email, DROP contact_phone, DROP created_at, DROP auth_token, DROP access_token');
    }
}
