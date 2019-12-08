<?php

namespace App\Service\CurrencyService\Operation;

use App\Entity\Wallet;
use App\Entity\WalletOperation;
use App\Service\CurrencyService\Money;
use App\Service\CurrencyService\MoneyConverter;
use App\Service\UserService\UserService;

class OperationService
{
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var MoneyConverter
     */
    private $moneyConverter;

    public function __construct(UserService $userService, MoneyConverter $moneyConverter)
    {
        $this->userService = $userService;
        $this->moneyConverter = $moneyConverter;
    }

    public function update(Wallet $wallet, Money $money, string $cause)
    {
        $convertMoney = $this->moneyConverter->convert($wallet->getCurrency(), $money);
        $wallet->setValue($wallet->getValue() + $convertMoney->getValue());

        $operation = new WalletOperation();
//        $operation->setWallet($wallet);
        $operation->setWalletValue($wallet->getValue());
        $operation->setValue($convertMoney->getValue());
        $operation->setBaseValue($money->getValue());
        $operation->setBaseCurrency($money->getCurrency());
        $operation->setCause($cause);
        $operation->setDateCreate(new \DateTime('now'));

        $wallet->addOperation($operation);
    }
}
