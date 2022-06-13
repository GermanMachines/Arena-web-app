<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413223210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABF165E2F');
        $this->addSql('DROP INDEX FK_B3BA5A5ABF165E2F ON products');
        $this->addSql('ALTER TABLE products CHANGE idCat idCat INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products CHANGE idCat idCat INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABF165E2F FOREIGN KEY (idCat) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX FK_B3BA5A5ABF165E2F ON products (idCat)');
    }
}
