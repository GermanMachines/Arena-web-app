<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoryreclamation
 *
 * @ORM\Table(name="categoryreclamation")
 * @ORM\Entity
 */
class Categoryreclamation
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="name is required")
     * @Assert\Length(
     *      min = 4,
     *      minMessage=" Name must be at least 4 characters long"
     *
     *     )
     */
    private $nom;

    public function getId(): ?int
    {
        return $this->id;
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
    public function __toString()
    {
        return strval($this->id);
    }
}
