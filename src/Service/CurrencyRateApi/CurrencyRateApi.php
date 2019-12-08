<?php

namespace App\Service\CurrencyRateApi;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Service\CurrencyRateApi\Rate;

class CurrencyRateApi
{
    const API_URL = 'https://api.exchangeratesapi.io/latest';


    /**
     * @param Currency $root
     * @param CurrencyRepository $currencyRepository
     * @return Rate[]
     */
    public function response(Currency $root, CurrencyRepository $currencyRepository)
    {
        $currencies = $currencyRepository->findAllGroupByName();
        unset($currencies[$root->getName()]);

        $context = file_get_contents(self::API_URL . '?' . http_build_query([
                'base' => $root->getName(),
                'symbols' => implode(',', array_keys($currencies)),
            ]));
        $response = json_decode($context, true);
        foreach ($response['rates'] as  $currencyName => $value) {
            $currency = $currencies[$currencyName] ?? null;
            yield new Rate($currency, (int) ($currency->getRation() * $value));
        }
    }
}
