<?php

namespace App\Service\CurrencyService;

use App\Entity\Currency;
use App\Exception\DomainException;

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
     * @param Currency $base
     * @param Money $money
     * @return Money
     * @throws DomainException
     */
    public function convert(Currency $base, Money $money): Money
    {
        if ($base->equals($money->getCurrency())) {
            return $money;
        }

        $baseCurrencyRate = $this->currencyService->getCurrencyRate($base);
        $moneyCurrencyRate = $this->currencyService->getCurrencyRate($money->getCurrency());

        return new Money($base, ($money->getValue() * $baseCurrencyRate->getValue()) / $moneyCurrencyRate->getValue());
    }
}
