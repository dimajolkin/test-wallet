<?php

namespace App\Service\CurrencyService\Operation;

use App\Entity\Wallet;
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
    /**
     * @var OperationFactory
     */
    private $operationFactory;

    public function __construct(
        UserService $userService,
        OperationFactory $operationFactory,
        MoneyConverter $moneyConverter
    ) {
        $this->userService = $userService;
        $this->moneyConverter = $moneyConverter;
        $this->operationFactory = $operationFactory;
    }

    /**
     * @param Wallet $wallet
     * @param Money $money
     * @param string $cause
     * @param string $type
     * @throws \App\Exception\DomainException
     */
    public function update(Wallet $wallet, Money $money, string $cause, string $type)
    {
        $convertMoney = $this->moneyConverter->convert($wallet->getCurrency(), $money);
        $wallet->setValue($wallet->getValue() + $convertMoney->getValue());
        $operation = $this->operationFactory->build($wallet, $money, $convertMoney, $cause);

        $wallet->addOperation($operation);
    }
}
