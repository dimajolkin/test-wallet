<?php

namespace App\Service\UserService;

use App\Entity\Wallet;
use App\Repository\CurrencyRepository;
use Symfony\Component\Validator\Exception\ValidatorException;

class WalletFactory
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function buildEmpty(?string $walletCurrencyName): Wallet
    {
        $currency = $walletCurrencyName !== null
            ? $this->currencyRepository->getByName($walletCurrencyName)
            : $this->currencyRepository->getRoot();
        if ($currency === null) {
            throw new ValidatorException("$walletCurrencyName not found");
        }

        $wallet = new Wallet();
        $wallet->setValue(0);
        $wallet->setCurrency($currency);
        $wallet->setDateCreate(new \DateTime('now'));
        $wallet->setDateUpdate(new \DateTime('now'));

        return $wallet;
    }
}
