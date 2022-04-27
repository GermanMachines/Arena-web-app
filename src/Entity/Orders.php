<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="num", type="integer", nullable=false)
     */
    private $num;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="id")
     * })
     */
    private $iduser;

    /**
     * @var int
     *
     * @ORM\Column(name="productQty", type="integer", nullable=false)
     */
    private $productqty;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="date", nullable=false)
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

    public function getIduser(): ?Users
    {
        return $this->iduser;
    }

    public function setIduser(Users $iduser): self
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
