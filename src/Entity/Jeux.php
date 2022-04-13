<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Jeux
 *
 * @ORM\Table(name="jeux")
 * @ORM\Entity
 */
class Jeux
{
    /**
     * @var int
     *
     * @ORM\Column(name="IdJeux", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idjeux;

    /**
     * @var string
     *
     * @Assert\NotBlank(message=" Nom doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un Nom au mini de 5 caracteres"
     *
     *     )
     * @ORM\Column(name="NomJeux", type="string", length=500, nullable=false)
     */
    private $nomjeux;

    /**
     * @var string
     *
     * @ORM\Column(name="ImageJeux", type="string", length=500, nullable=false)
     */
    private $imagejeux;

    public function getIdjeux(): ?int
    {
        return $this->idjeux;
    }

    public function getNomjeux(): ?string
    {
        return $this->nomjeux;
    }

    public function setNomjeux(string $nomjeux): self
    {
        $this->nomjeux = $nomjeux;

        return $this;
    }

    public function getImagejeux(): ?string
    {
        return $this->imagejeux;
    }

    public function setImagejeux(string $imagejeux): self
    {
        $this->imagejeux = $imagejeux;

        return $this;
    }


}
