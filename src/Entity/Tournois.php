<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @Groups("post:read")
     */
    private $idtournois;

    /**
     * @var string
     *
     * @ORM\Column(name="Titre", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date_debut", type="date", nullable=false)
     * @Groups("post:read")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date_fin", type="date", nullable=false)
     * @Groups("post:read")
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="DescriptionTournois", type="string", length=500, nullable=false)
     * @Groups("post:read")
     */
    private $descriptiontournois;

    /**
     * @var string
     *
     * @ORM\Column(name="Type", type="string", length=100, nullable=false)
     * @Groups("post:read")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="NbrParticipants", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $nbrparticipants;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Winner", type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $winner;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Status", type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $status;

    /**
     * @var \Jeux
     *
     * @ORM\ManyToOne(targetEntity="Jeux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="IdJeux", referencedColumnName="IdJeux")
     * })
     * @Groups("post:read")
     */
    private $idjeux;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Equipe", mappedBy="idtournois")
     * @Groups("post:read")
     */
    private $idequipe;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idequipe = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * @return Collection<int, Equipe>
     */
    public function getIdequipe(): Collection
    {
        return $this->idequipe;
    }

    public function addIdequipe(Equipe $idequipe): self
    {
        if (!$this->idequipe->contains($idequipe)) {
            $this->idequipe[] = $idequipe;
            $idequipe->addIdtournoi($this);
        }

        return $this;
    }

    public function removeIdequipe(Equipe $idequipe): self
    {
        if ($this->idequipe->removeElement($idequipe)) {
            $idequipe->removeIdtournoi($this);
        }

        return $this;
    }
    public function __toString() {
        return strval($this->idtournois);
    }
}
