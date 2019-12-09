<?php

namespace App\Entity;

use App\Service\CurrencyService\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WalletRepository")
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rest"})
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"rest"})
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"rest"})
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"rest"})
     */
    private $date_update;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WalletOperation", mappedBy="wallet", cascade={"persist", "remove"})
     */
    private $walletOperations;

    public function __construct()
    {
        $this->walletOperations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoney(): Money
    {
        return new Money($this->getCurrency(), $this->getValue());
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(\DateTimeInterface $date_create): self
    {
        $this->date_create = $date_create;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    /**
     * @return Collection|WalletOperation[]
     */
    public function getOperations(): Collection
    {
        return $this->walletOperations;
    }

    public function addOperation(WalletOperation $walletOperation): self
    {
        if (!$this->walletOperations->contains($walletOperation)) {
            $this->walletOperations[] = $walletOperation;
            $walletOperation->setWallet($this);
        }

        return $this;
    }

    public function removeOperation(WalletOperation $walletOperation): self
    {
        if ($this->walletOperations->contains($walletOperation)) {
            $this->walletOperations->removeElement($walletOperation);
            // set the owning side to null (unless already changed)
            if ($walletOperation->getWallet() === $this) {
                $walletOperation->setWallet(null);
            }
        }

        return $this;
    }
}
