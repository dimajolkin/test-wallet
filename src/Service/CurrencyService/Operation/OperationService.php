<?php

namespace App\Service\CurrencyService\Operation;

use App\Entity\Wallet;
use App\Exception\ValidationException;
use App\Service\CurrencyService\Money;
use App\Service\CurrencyService\MoneyConverter;
use App\Service\UserService\UserService;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        UserService $userService,
        OperationFactory $operationFactory,
        ValidatorInterface $validator,
        MoneyConverter $moneyConverter
    ) {
        $this->userService = $userService;
        $this->moneyConverter = $moneyConverter;
        $this->operationFactory = $operationFactory;
        $this->validator = $validator;
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
        $operation = $this->operationFactory->build($wallet, $money, $convertMoney, $cause, $type);
        $validationList = $this->validator->validate($operation);
        if ($validationList->count() === 0) {
            $wallet->addOperation($operation);
        } else {
            throw new ValidationException($validationList);
        }
    }
}
