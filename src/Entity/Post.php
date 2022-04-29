<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */

class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_post", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPost;

    /**
     * @var string
     * @Assert\NotBlank(message=" titre doit etre non vide")
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     */
    private $titre;

    /**
     * @var string
     * @Assert\NotBlank(message=" auteur doit etre non vide")
     * @ORM\Column(name="auteur", type="string", length=255, nullable=false)
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="img_post", type="string", length=255, nullable=false)
     */
    private $imgPost;

    /**
     * @var string
     * @Assert\Date(message=" date doit etre non vide")
     * @ORM\Column(name="date_post", type="string", length=255, nullable=false)
     */
    private $datePost;

    /**
     * @var int
     * @Assert\NotBlank(message=" rate doit etre non vide")
     * @Assert\Length(
     *     
     *      max = 1,
     *      maxMessage=" Entrer un titre au max de 5 caracteres"
     *
     *     )
     * @ORM\Column(name="rate", type="integer", nullable=false)
     */
    private $rate = '0';

    public function getIdPost(): ?int
    {
        return $this->idPost;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getImgPost(): ?string
    {
        return $this->imgPost;
    }

    public function setImgPost(string $imgPost): self
    {
        $this->imgPost = $imgPost;

        return $this;
    }

    public function getDatePost(): ?string
    {
        return $this->datePost;
    }

    public function setDatePost(string $datePost): self
    {
        $this->datePost = $datePost;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }
    public function __toString()
    {
        return $this->titre;
    }


}
