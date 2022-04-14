<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire")
 * @ORM\Entity
 */
class Commentaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_com", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_com", type="string", length=255, nullable=false)
     */
    private $nomCom;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_com", type="string", length=255, nullable=false)
     */
    private $descCom;

    public function getIdCom(): ?int
    {
        return $this->idCom;
    }

    public function getNomCom(): ?string
    {
        return $this->nomCom;
    }

    public function setNomCom(string $nomCom): self
    {
        $this->nomCom = $nomCom;

        return $this;
    }

    public function getDescCom(): ?string
    {
        return $this->descCom;
    }

    public function setDescCom(string $descCom): self
    {
        $this->descCom = $descCom;

        return $this;
    }


}
