<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"rest"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"rest"})
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Wallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rest"})
     */
    private $wallet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet_id): self
    {
        $this->wallet = $wallet_id;

        return $this;
    }
}
