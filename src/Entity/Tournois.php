<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Tournois
 *
 * @ORM\Table(name="tournois", indexes={@ORM\Index(name="fk_1", columns={"IdJeux"})})
 * @ORM\Entity
 */
class Tournois
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdTournois", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtournois;

    /**
     * @var string
     * @Assert\NotBlank(message=" Titre doit etre non vide")
     * @ORM\Column(name="Titre", type="string", length=100, nullable=false)
     */
    private $titre;

    /**
     * @var \DateTime
     * @Assert\GreaterThan("today UTC")
     * @ORM\Column(name="Date_debut", type="date", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     * @Assert\GreaterThan(propertyPath="Date_debut")
     * @ORM\Column(name="Date_fin", type="date", nullable=false)
     */
    private $dateFin;

    /**
     * @var string
     * @Assert\NotBlank(message="description  doit etre non vide")
     * @Assert\Length(
     *      min = 7,
     *      max = 100,
     *      minMessage = "doit etre >=7 ",
     *      maxMessage = "doit etre <=100" )
     * @ORM\Column(name="DescriptionTournois", type="string", length=500, nullable=false)
     */
    private $descriptiontournois;

    /**
     * @var string
     * @Assert\NotBlank(message=" Type doit etre non vide")
     * @ORM\Column(name="Type", type="string", length=100, nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @Assert\GreaterThan(
     * value = 1
     * )
     * @ORM\Column(name="NbrParticipants", type="integer", nullable=false)
     */
    private $nbrparticipants;

    /**
     * @var string|null
     * @Assert\NotBlank(message=" Type doit etre non vide")
     * @ORM\Column(name="Winner", type="string", length=255, nullable=true)
     */
    private $winner;

    /**
     * @var string|null
     * @Assert\NotBlank(message=" Type doit etre non vide")
     * @ORM\Column(name="Status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var \Jeux
     *
     * @ORM\ManyToOne(targetEntity="Jeux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IdJeux", referencedColumnName="IdJeux")
     * })
     */
    private $idjeux;

    public function getIdtournois(): ?int
    {
        return $this->idtournois;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDescriptiontournois(): ?string
    {
        return $this->descriptiontournois;
    }

    public function setDescriptiontournois(string $descriptiontournois): self
    {
        $this->descriptiontournois = $descriptiontournois;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbrparticipants(): ?int
    {
        return $this->nbrparticipants;
    }

    public function setNbrparticipants(int $nbrparticipants): self
    {
        $this->nbrparticipants = $nbrparticipants;

        return $this;
    }

    public function getWinner(): ?string
    {
        return $this->winner;
    }

    public function setWinner(?string $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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

    
    public function __toString() {
        return strval($this->idtournois);
    }


}
