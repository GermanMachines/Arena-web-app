<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414032823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY fk_id_product');
        $this->addSql('ALTER TABLE orders CHANGE idProduct idProduct INT DEFAULT NULL, CHANGE idUser idUser INT DEFAULT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEFE6E88D7 FOREIGN KEY (idUser) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEC3F36F5F FOREIGN KEY (idProduct) REFERENCES products (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEFE6E88D7 ON orders (idUser)');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABF165E2F');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABF165E2F FOREIGN KEY (idCat) REFERENCES categories (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEFE6E88D7');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEC3F36F5F');
        $this->addSql('DROP INDEX IDX_E52FFDEEFE6E88D7 ON orders');
        $this->addSql('ALTER TABLE orders CHANGE idUser idUser INT NOT NULL, CHANGE idProduct idProduct INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT fk_id_product FOREIGN KEY (idProduct) REFERENCES products (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5ABF165E2F');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5ABF165E2F FOREIGN KEY (idCat) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
