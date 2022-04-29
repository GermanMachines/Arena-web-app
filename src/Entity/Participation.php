<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation", indexes={@ORM\Index(name="FK_jeux_tournois", columns={"IdTournois"}), @ORM\Index(name="FK_jeux_t", columns={"IdEquipe"})})
 * @ORM\Entity
 */
class Participation
{
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
     * @ORM\Column(name="IdTournois", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idtournois;

    public function getIdequipe(): ?int
    {
        return $this->idequipe;
    }

    public function getIdtournois(): ?int
    {
        return $this->idtournois;
    }


}
