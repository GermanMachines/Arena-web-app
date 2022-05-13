<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity ;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields = {"username"},
 *     message="le username existe déjà"
 * )
 */
class User implements UserInterface
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
     * @ORM\Column(name="surnom", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "le surnom doit contenir au minimum {{ limit }} caracteres",
     *      maxMessage = "le surnom doit contenir au plus {{ limit }} caracteres"
     * )
     * @Groups("post:read")
     */
    private $surnom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "le nom doit contenir au minimum {{ limit }} caracteres",
     *      maxMessage = "le nom doit contenir au plus {{ limit }} caracteres"
     * )
     * @Groups("post:read")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Image", type="string", length=255, nullable=false)
     * @Groups("post:read")
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Groups("post:read")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=20, nullable=false)
     *  @Assert\Length(
     *      min = 8,
     *      minMessage = "Votre mot de passe doit comporter au minimum {{ limit }} caractères")
     * @Groups("post:read")
     */
    private $mdp;

    /**
     *  @Assert\EqualTo(propertyPath = "mdp", message="Vous n'avez pas passé le même mot de passe !" )
     * @Groups("post:read")
     */
    private $confirmpassword;

    public function getconfirmpassword()
    {
        return $this->confirmpassword;
    }

    public function setconfirmpassword($confirmpassword)
    {
        $this->confirmpassword = $confirmpassword;

        return $this;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=8, nullable=false)
     * @Groups("post:read")
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=15, nullable=false)
     * @Groups("post:read")
     */
    private $role;

    /**
     * @var string|null
     *
     * @ORM\Column(name="block", type="string", length=3, nullable=true)
     * @Groups("post:read")
     */
    private $block;

    /**
     * @var \Equipe
     *
     * @ORM\ManyToOne(targetEntity="Equipe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_equipe", referencedColumnName="idEquipe")
     * })
     * @Groups("post:read")
     */
    private $idEquipe;

    protected $captchaCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $roles;

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurnom(): ?string
    {
        return $this->surnom;
    }

    public function setSurnom(string $surnom): self
    {
        $this->surnom = $surnom;

        return $this;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(?string $block): self
    {
        $this->block = $block;

        return $this;
    }

    public function getIdEquipe(): ?Equipe
    {
        return $this->idEquipe;
    }

    public function setIdEquipe(?Equipe $idEquipe): self
    {
        $this->idEquipe = $idEquipe;

        return $this;
    }

    public function __toString() {
        return strval($this->id);
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->mdp,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->mdp,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }



    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }


    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function getRoles()
    {
        return [$this->roles];
    }

    public function setRoles(?string $roles) :self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
        return $this->mdp;
    }

    public function setPassword(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }
}
