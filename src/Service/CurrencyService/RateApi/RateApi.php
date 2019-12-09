<?php

namespace App\Service\CurrencyService\RateApi;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;

class RateApi
{
    const API_URL = 'https://api.exchangeratesapi.io/latest';

    /**
     * @param Currency $root
     * @param CurrencyRepository $currencyRepository
     * @return Rate[]
     */
    public function getRates(Currency $root, CurrencyRepository $currencyRepository): iterable
    {
        $currencies = $currencyRepository->findAllGroupByName();
        $root = $currencyRepository->getRoot();
        $context = file_get_contents(self::API_URL . '?' . http_build_query([
                'base' => $root->getName(),
                'symbols' => implode(',', array_keys($currencies)),
            ]));
        $response = json_decode($context, true);
        foreach ($response['rates'] as  $currencyName => $value) {
            $currency = $currencies[$currencyName] ?? null;
            yield new Rate($currency, (int) ($value * $currency->getRation()));
        }
    }
}
