<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426190132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE idUtulisateur idUtulisateur INT DEFAULT NULL, CHANGE idJeux idJeux INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE id_user id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE matchs CHANGE IdTournois IdTournois INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE rate rate INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY id_cat_rec');
        $this->addSql('ALTER TABLE reclamation CHANGE idUser idUser INT DEFAULT NULL, CHANGE idCategoryReclamation idCategoryReclamation INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064048C9C8A0 FOREIGN KEY (idCategoryReclamation) REFERENCES categoryreclamation (id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(15) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis CHANGE idJeux idJeux INT NOT NULL, CHANGE idUtulisateur idUtulisateur INT NOT NULL');
        $this->addSql('ALTER TABLE commentaire CHANGE id_user id_user INT NOT NULL');
        $this->addSql('ALTER TABLE matchs CHANGE IdTournois IdTournois INT NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE rate rate INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064048C9C8A0');
        $this->addSql('ALTER TABLE reclamation CHANGE idUser idUser INT NOT NULL, CHANGE idCategoryReclamation idCategoryReclamation INT NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT id_cat_rec FOREIGN KEY (idCategoryReclamation) REFERENCES categoryreclamation (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP username, CHANGE role role VARCHAR(15) DEFAULT NULL');
    }
}
