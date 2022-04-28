<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412023047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournois DROP FOREIGN KEY FK_D7AAF974304EEA5');
        $this->addSql('DROP INDEX fk_d7aaf974304eea5 ON tournois');
        $this->addSql('CREATE INDEX fk_1 ON tournois (IdJeux)');
        $this->addSql('ALTER TABLE tournois ADD CONSTRAINT FK_D7AAF974304EEA5 FOREIGN KEY (IdJeux) REFERENCES jeux (IdJeux)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournois DROP FOREIGN KEY FK_D7AAF974304EEA5');
        $this->addSql('DROP INDEX fk_1 ON tournois');
        $this->addSql('CREATE INDEX FK_D7AAF974304EEA5 ON tournois (IdJeux)');
        $this->addSql('ALTER TABLE tournois ADD CONSTRAINT FK_D7AAF974304EEA5 FOREIGN KEY (IdJeux) REFERENCES jeux (IdJeux)');
    }
}
