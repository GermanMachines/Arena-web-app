<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413230702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABF165E2F');
        $this->addSql('DROP INDEX fk_b3ba5a5abf165e2f ON products');
        $this->addSql('CREATE INDEX IDX_B3BA5A5ABF165E2F ON products (idCat)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABF165E2F FOREIGN KEY (idCat) REFERENCES categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABF165E2F');
        $this->addSql('DROP INDEX idx_b3ba5a5abf165e2f ON products');
        $this->addSql('CREATE INDEX FK_B3BA5A5ABF165E2F ON products (idCat)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABF165E2F FOREIGN KEY (idCat) REFERENCES categories (id)');
    }
}
