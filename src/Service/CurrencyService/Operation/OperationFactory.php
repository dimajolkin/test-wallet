<?php

namespace App\Service\CurrencyService\Operation;

use App\Entity\Wallet;
use App\Entity\WalletOperation;
use App\Service\CurrencyService\CurrencyService;
use App\Service\CurrencyService\Money;

class OperationFactory
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function build(Wallet $wallet, Money $base, Money $money, string $cause): WalletOperation
    {
        $operation = new WalletOperation();
        $operation->setCurrencyRate($this->currencyService->getRootRate());
        $operation->setWalletValue($wallet->getValue());
        $operation->setValue($money->getValue());
        $operation->setBaseValue($base->getValue());
        $operation->setBaseCurrency($base->getCurrency());
        $operation->setCause($cause);
        $operation->setDateCreate(new \DateTime('now'));

        return $operation;
    }
}
