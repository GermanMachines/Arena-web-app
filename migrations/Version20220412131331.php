<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412131331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, score INT NOT NULL, commentaire TEXT NOT NULL, idUtulisateur INT NOT NULL, idJeux INT NOT NULL, INDEX fk_foreign_key_idjeux (idJeux), INDEX fk_user_idd (idUtulisateur), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categoryreclamation (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id_com INT AUTO_INCREMENT NOT NULL, id_user INT DEFAULT NULL, desc_com VARCHAR(255) NOT NULL, date_com VARCHAR(255) NOT NULL, id_post INT NOT NULL, INDEX fk_userr_comm (id_user), INDEX id_post (id_post), PRIMARY KEY(id_com)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe (idEquipe INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, score INT NOT NULL, region VARCHAR(255) NOT NULL, PRIMARY KEY(idEquipe)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeux (IdJeux INT AUTO_INCREMENT NOT NULL, NomJeux VARCHAR(500) NOT NULL, ImageJeux VARCHAR(500) NOT NULL, PRIMARY KEY(IdJeux)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE match_eq (idMatch INT NOT NULL, IdEquipe INT NOT NULL, Score INT NOT NULL, INDEX FK_match_eq (idMatch), INDEX FK_match_eq1 (IdEquipe), PRIMARY KEY(idMatch, IdEquipe)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matchs (idMatch INT AUTO_INCREMENT NOT NULL, IdTournois INT NOT NULL, DateMatch DATETIME DEFAULT \'current_timestamp()\' NOT NULL, Reference VARCHAR(255) DEFAULT \'NULL\', INDEX FK_t (IdTournois), PRIMARY KEY(idMatch)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, num INT NOT NULL, idProduct INT NOT NULL, idUser INT NOT NULL, productQty INT NOT NULL, createdAt DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (IdEquipe INT NOT NULL, IdTournois INT NOT NULL, INDEX FK_jeux_tournois (IdTournois), INDEX FK_jeux_t (IdEquipe), PRIMARY KEY(IdEquipe, IdTournois)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id_post INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, img_post VARCHAR(255) NOT NULL, date_post VARCHAR(255) NOT NULL, rate INT NOT NULL, PRIMARY KEY(id_post)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, qty INT NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, idCat INT NOT NULL, rate INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, categoryreclamation_id INT NOT NULL, titre VARCHAR(30) NOT NULL, message TEXT NOT NULL, idUser INT NOT NULL, idCategoryReclamation INT NOT NULL, etat TINYINT(1) NOT NULL, date DATE DEFAULT \'current_timestamp()\' NOT NULL, INDEX IDX_CE6064049CE0C7BC (categoryreclamation_id), INDEX fk_user_id (idUser), INDEX id_cat_rec (idCategoryReclamation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournois (IdTournois INT AUTO_INCREMENT NOT NULL, Titre VARCHAR(100) NOT NULL, Date_debut DATE NOT NULL, Date_fin DATE NOT NULL, DescriptionTournois VARCHAR(500) NOT NULL, Type VARCHAR(100) NOT NULL, NbrParticipants INT NOT NULL, IdJeux INT NOT NULL, Winner VARCHAR(255) DEFAULT \'NULL\', Status VARCHAR(255) DEFAULT \'NULL\', INDEX fk_J (IdJeux), UNIQUE INDEX Titre (Titre), PRIMARY KEY(IdTournois)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, surnom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, Image VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mdp VARCHAR(20) NOT NULL, telephone VARCHAR(8) NOT NULL, id_equipe INT DEFAULT NULL, role VARCHAR(15) NOT NULL, block VARCHAR(3) DEFAULT \'NULL\', INDEX fk_idequipe (id_equipe), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE6064049CE0C7BC FOREIGN KEY (categoryreclamation_id) REFERENCES categoryreclamation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE6064049CE0C7BC');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6B3CA4B');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categoryreclamation');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE jeux');
        $this->addSql('DROP TABLE match_eq');
        $this->addSql('DROP TABLE matchs');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE tournois');
        $this->addSql('DROP TABLE user');
    }
}
