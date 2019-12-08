<?php


namespace App\Service\CurrencyService;


use App\Entity\Currency;

class Money
{
    /** @var Currency */
    private $currency;
    /** @var int  */
    private $value;

    public function __construct(Currency $currency, int $value)
    {
        $this->currency = $currency;
        $this->value = $value;
    }

}
