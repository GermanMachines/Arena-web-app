<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Equipe
 *
 * @ORM\Table(name="equipe")
 * @ORM\Entity
 */
class Equipe
{
    /**
     * @var int
     *
     * @ORM\Column(name="idEquipe", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idequipe;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=false)
     */
    private $logo;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255, nullable=false)
     */
    private $region;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Matchs", mappedBy="idequipe")
     */
    private $idmatch;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Tournois", inversedBy="idequipe")
     * @ORM\JoinTable(name="participation",
     *   joinColumns={
     *     @ORM\JoinColumn(name="IdEquipe", referencedColumnName="idEquipe")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="IdTournois", referencedColumnName="IdTournois")
     *   }
     * )
     */
    private $idtournois;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idmatch = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idtournois = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdequipe(): ?int
    {
        return $this->idequipe;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, Matchs>
     */
    public function getIdmatch(): Collection
    {
        return $this->idmatch;
    }

    public function addIdmatch(Matchs $idmatch): self
    {
        if (!$this->idmatch->contains($idmatch)) {
            $this->idmatch[] = $idmatch;
            $idmatch->addIdequipe($this);
        }

        return $this;
    }

    public function removeIdmatch(Matchs $idmatch): self
    {
        if ($this->idmatch->removeElement($idmatch)) {
            $idmatch->removeIdequipe($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tournois>
     */
    public function getIdtournois(): Collection
    {
        return $this->idtournois;
    }

    public function addIdtournoi(Tournois $idtournoi): self
    {
        if (!$this->idtournois->contains($idtournoi)) {
            $this->idtournois[] = $idtournoi;
        }

        return $this;
    }

    public function removeIdtournoi(Tournois $idtournoi): self
    {
        $this->idtournois->removeElement($idtournoi);

        return $this;
    }
}
