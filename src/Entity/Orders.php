<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Orders
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="fk_id_product", columns={"idProduct"}), @ORM\Index(name="fk_id_user", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 */
class Orders
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
     * @var int
     *
     * @ORM\Column(name="num", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $num;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $iduser;

    /**
     * @var int
     *
     * @ORM\Column(name="productQty", type="integer", nullable=false)
     * @Groups("post:read")
     */
    private $productqty;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="date", nullable=false)
     * @Groups("post:read")
     */
    private $createdat;
    public function __construct()
    {
        $this->createdat = new \DateTime();
    }

    /**
     * @var \Products
     *
     * @ORM\ManyToOne(targetEntity="Products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProduct", referencedColumnName="id")
     * })
     * @Groups("post:read")
     */
    private $idproduct;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(User $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getProductqty(): ?int
    {
        return $this->productqty;
    }

    public function setProductqty(int $productqty): self
    {
        $this->productqty = $productqty;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeInterface $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getIdproduct(): ?Products
    {
        return $this->idproduct;
    }

    public function setIdproduct(?Products $idproduct): self
    {
        $this->idproduct = $idproduct;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->createdat;
    }
}
