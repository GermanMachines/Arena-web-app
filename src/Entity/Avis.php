<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Avis
 *
 * @ORM\Table(name="avis", indexes={@ORM\Index(name="fkkk_id_jeux", columns={"idJeux"}), @ORM\Index(name="fkkk_id_utulisateur", columns={"idUtulisateur"})})
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
     *  @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      notInRangeMessage = "You must rate between 1 and 5.",
     * )
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=65535, nullable=false)
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Commentaire must be at least 5 characters long"
     *
     *     )
     */
    private $commentaire;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUtulisateur", referencedColumnName="id")
     * })
     */
    private $idutulisateur;

    /**
     * @var \Jeux
     *
     * @ORM\ManyToOne(targetEntity="Jeux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idJeux", referencedColumnName="IdJeux")
     * })
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

    public function getIdutulisateur(): ?User
    {
        return $this->idutulisateur;
    }

    public function setIdutulisateur(?User $idutulisateur): self
    {
        $this->idutulisateur = $idutulisateur;

        return $this;
    }

    public function getIdjeux(): ?Jeux
    {
        return $this->idjeux;
    }

    public function setIdjeux(?Jeux $idjeux): self
    {
        $this->idjeux = $idjeux;

        return $this;
    }
    public function __toString()
    {
        return strval($this->id);
    }
}
