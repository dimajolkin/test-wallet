<?php

namespace App\Service\UserService;

use App\Entity\Wallet;
use App\Service\CurrencyService\CurrencyService;

class WalletFactory
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function buildEmpty(?string $walletCurrencyName): Wallet
    {
        $currency = $this->currencyService->getCurrency($walletCurrencyName);

        $wallet = new Wallet();
        $wallet->setValue(0);
        $wallet->setCurrency($currency);
        $wallet->setDateCreate(new \DateTime('now'));
        $wallet->setDateUpdate(new \DateTime('now'));

        return $wallet;
    }
}
