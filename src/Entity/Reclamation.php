<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Reclamation
 *
 * @ORM\Table(name="reclamation", indexes={@ORM\Index(name="fk_id_user", columns={"idUser"}), @ORM\Index(name="fk_category_rec", columns={"idCategoryReclamation"})})
 * @ORM\Entity(repositoryClass="App\Repository\ReclamationRepository")
 */
class Reclamation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="Title is required")
     * @Groups("post:read")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" title must be at least 5 characters long"
     *
     *     )
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65535, nullable=false)
     * @Assert\NotBlank(message="Message is required")
     * @Groups("post:read")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Message must be at least 255 characters long"
     *
     *     )
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="etat", type="boolean", nullable=true)
     * @Groups("post:read")
     */
    private $etat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     * @Groups("post:read")
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups("post:read")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @var \Categoryreclamation
     *
     * @ORM\ManyToOne(targetEntity="Categoryreclamation")
     * @Groups("post:read")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCategoryReclamation", referencedColumnName="id")
     * })
     */
    private $idcategoryreclamation;


    private $idcat;


    public function getIdcCat(): ?int
    {
        return $this->idcat;
    }

    public function setIdCat($idcat)
    {
        $this->$idcat = $idcat;
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdcategoryreclamation(): ?Categoryreclamation
    {
        return $this->idcategoryreclamation;
    }

    public function setIdcategoryreclamation(?Categoryreclamation $idcategoryreclamation): self
    {
        $this->idcategoryreclamation = $idcategoryreclamation;

        return $this;
    }
    public function __toString()
    {
        return strval($this->id);
    }
}
