<?php

namespace App\Service\CurrencyService;

use App\Entity\Wallet;

class MoneyFactory
{
    /**
     * @var CurrencyService
     */
    private $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function build(Wallet $wallet, ?string $currency, float $value): Money
    {
        $currency = $this->currencyService->getCurrency($currency, $wallet->getCurrency());
        return new Money($currency, (int) ($currency->getRation() * $value));
    }
}
