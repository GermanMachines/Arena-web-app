<?php

namespace App\Entity;

use App\Repository\EmailReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailReclamationRepository::class)
 */
class EmailReclamation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reciever;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReciever(): ?string
    {
        return $this->reciever;
    }

    public function setReciever(string $reciever): self
    {
        $this->reciever = $reciever;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
