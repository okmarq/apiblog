<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220101020239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE modified_at modified_at DATETIME on update CURRENT_TIMESTAMP');
        $this->addSql('DROP INDEX id ON role');
        $this->addSql('ALTER TABLE vrequest CHANGE modified_at modified_at DATETIME on update CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post CHANGE modified_at modified_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX id ON role (id, role_name)');
        $this->addSql('ALTER TABLE vrequest CHANGE modified_at modified_at DATETIME DEFAULT NULL');
    }
}
