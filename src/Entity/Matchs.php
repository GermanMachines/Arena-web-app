<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Matchs
 *
 * @ORM\Table(name="matchs", indexes={@ORM\Index(name="FK_t", columns={"IdTournois"})})
 * @ORM\Entity
 */
class Matchs
{
    /**
     * @var int
     *
     * @ORM\Column(name="idMatch", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmatch;

    /**
     * @var int
     *
     * @ORM\Column(name="IdTournois", type="integer", nullable=false)
     */
    private $idtournois;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateMatch", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datematch = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="Reference", type="string", length=255, nullable=true)
     */
    private $reference;

    public function getIdmatch(): ?int
    {
        return $this->idmatch;
    }

    public function getIdtournois(): ?int
    {
        return $this->idtournois;
    }

    public function setIdtournois(int $idtournois): self
    {
        $this->idtournois = $idtournois;

        return $this;
    }

    public function getDatematch(): ?\DateTimeInterface
    {
        return $this->datematch;
    }

    public function setDatematch(\DateTimeInterface $datematch): self
    {
        $this->datematch = $datematch;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }


}
