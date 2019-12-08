<?php

namespace App\Service\CurrencyService;

use App\Entity\Currency;

class MoneyConverter
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @param Currency $resultCurrency
     * @param Money $money
     * @return Money
     * @throws \App\Exception\DomainException
     */
    public function convert(Currency $resultCurrency, Money $money): Money
    {
        $root = $this->currencyService->getRoot();
        $actualValue = $this->currencyService->getActualValue();
        if ($resultCurrency->equals($money->getCurrency())) {
            return $money;
        }

        if (!$money->getCurrency()->equals($root)) {
            return new Money($resultCurrency, $money->getValue() / $actualValue);
        }

        return new Money($resultCurrency, $money->getValue() * $actualValue);
    }
}
