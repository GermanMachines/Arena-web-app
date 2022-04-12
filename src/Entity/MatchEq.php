<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MatchEq
 *
 * @ORM\Table(name="match_eq", indexes={@ORM\Index(name="FK_match_eq", columns={"idMatch"}), @ORM\Index(name="FK_match_eq1", columns={"IdEquipe"})})
 * @ORM\Entity
 */
class MatchEq
{
    /**
     * @var int
     *
     * @ORM\Column(name="idMatch", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idmatch;

    /**
     * @var int
     *
     * @ORM\Column(name="IdEquipe", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idequipe;

    /**
     * @var int
     *
     * @ORM\Column(name="Score", type="integer", nullable=false)
     */
    private $score;

    public function getIdmatch(): ?int
    {
        return $this->idmatch;
    }

    public function getIdequipe(): ?int
    {
        return $this->idequipe;
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


}
