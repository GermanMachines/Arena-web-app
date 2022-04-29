<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avis
 *
 * @ORM\Table(name="avis", indexes={@ORM\Index(name="fk_user_idd", columns={"idUtulisateur"}), @ORM\Index(name="fk_foreign_key_idjeux", columns={"idJeux"})})
 * @ORM\Entity
 */
class Avis
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=false)
     */
    private $commentaire;

    /**
     * @var int
     *
     * @ORM\Column(name="idUtulisateur", type="integer", nullable=false)
     */
    private $idutulisateur;

    /**
     * @var int
     *
     * @ORM\Column(name="idJeux", type="integer", nullable=false)
     */
    private $idjeux;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getIdutulisateur(): ?int
    {
        return $this->idutulisateur;
    }

    public function setIdutulisateur(int $idutulisateur): self
    {
        $this->idutulisateur = $idutulisateur;

        return $this;
    }

    public function getIdjeux(): ?int
    {
        return $this->idjeux;
    }

    public function setIdjeux(int $idjeux): self
    {
        $this->idjeux = $idjeux;

        return $this;
    }


}
