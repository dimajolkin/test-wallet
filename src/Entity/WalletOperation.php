<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WalletOperationRepository")
 */
class WalletOperation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wallet", inversedBy="walletOperations")
     */
    private $wallet;

    /**
     * @ORM\Column(type="integer")
     */
    private $wallet_value;

    /**
     * @ORM\Column(type="integer")
     *  @Assert\NotBlank()
     */
    private $value;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=CauseEnum::ALL, message="Choose a valid cause.")
     */
    private $cause;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Choice(choices=TypeEnum::ALL, message="Choose a valid type.")
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $base_value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Currency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $base_currency;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CurrencyRate", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $currency_rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_create;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getCurrencyRate(): CurrencyRate
    {
        return $this->currency_rate;
    }

    public function setCurrencyRate(CurrencyRate $currencyRate): void
    {
        $this->currency_rate = $currencyRate;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getWalletValue(): ?int
    {
        return $this->wallet_value;
    }

    public function setWalletValue(int $wallet_value): self
    {
        $this->wallet_value = $wallet_value;

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

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getBaseValue(): ?int
    {
        return $this->base_value;
    }

    public function setBaseValue(int $base_value): self
    {
        $this->base_value = $base_value;

        return $this;
    }

    public function getBaseCurrency(): ?Currency
    {
        return $this->base_currency;
    }

    public function setBaseCurrency(Currency $base_currency): self
    {
        $this->base_currency = $base_currency;

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
}
