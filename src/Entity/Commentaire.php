<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="id_post", columns={"id_post"}), @ORM\Index(name="fk_userr_comm", columns={"id_user"})})
 * @ORM\Entity(repositoryClass="App\Repository\CommentaireRepository")
 */
class Commentaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_com", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups("post:read")
     */
    private $idCom;

    /**
     * @var string
     * @Assert\NotBlank(message=" description doit etre non vide")
     * @ORM\Column(name="desc_com", type="string", length=255, nullable=false)
     * @Groups("post:read")
     */
    private $descCom;

    /**
     * @var string
     * @Assert\Date(message=" date doit etre non vide")
     * @ORM\Column(name="date_com", type="string", length=255, nullable=false)
     * @Groups("post:read")
     */
    private $dateCom;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $idUser;

    /**
     * @var \Post
     *
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_post", referencedColumnName="id_post")
     * })
     * @Groups("post:read")
     */
    private $idPost;

    public function getIdCom(): ?int
    {
        return $this->idCom;
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

    public function getDateCom(): ?string
    {
        return $this->dateCom;
    }

    public function setDateCom(string $dateCom): self
    {
        $this->dateCom = $dateCom;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdPost(): ?Post
    {
        return $this->idPost;
    }

    public function setIdPost(?Post $idPost): self
    {
        $this->idPost = $idPost;

        return $this;
    }


}
