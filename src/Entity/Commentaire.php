<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="id_post", columns={"id_post"}), @ORM\Index(name="fk_userr_comm", columns={"id_user"})})
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
     * @ORM\Column(name="desc_com", type="string", length=255, nullable=false)
     */
    private $descCom;

    /**
     * @var string
     *
     * @ORM\Column(name="date_com", type="string", length=255, nullable=false)
     */
    private $dateCom;

    /**
     * @var int
     *
     * @ORM\Column(name="id_post", type="integer", nullable=false)
     */
    private $idPost;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $idUser;

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

    public function getIdPost(): ?int
    {
        return $this->idPost;
    }

    public function setIdPost(int $idPost): self
    {
        $this->idPost = $idPost;

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


}
