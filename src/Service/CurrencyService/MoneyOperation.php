<?php

namespace App\Service\CurrencyService;

use App\Entity\Currency;

class MoneyOperation
{
    public function convert(Currency $currency, Money $money): Money
    {
        return $money;
    }
}
