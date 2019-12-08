<?php

namespace App\Service\CurrencyService\RateApi;

use App\Entity\Currency;

class Rate
{
    /** @var Currency */
    private $currency;

    /** @var int */
    private $value;

    /**
     * Rate constructor.
     * @param Currency $currency
     * @param int $value
     */
    public function __construct(Currency $currency, int $value)
    {
        $this->currency = $currency;
        $this->value = $value;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
