<?php

namespace App\Tests\unit;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Service\CurrencyService\CurrencyService;
use App\Service\CurrencyService\Money;
use App\Service\CurrencyService\MoneyConverter;
use Codeception\TestCase\Test;

class MoneyConverterTest extends Test
{
    /** @var MoneyConverter */
    private $converter;
    private $rub;
    private $usd;
    private const RATES = [
        'USD' => 1,
        'RUB' => self::RUB_RATE,
    ];

    private const RUB_RATE = 7000;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->init();
    }

    public function init()
    {
        $this->rub = $this->make(Currency::class, ['id' => 1, 'name' => 'RUB', 'ration' => 100]);
        $this->usd = $this->make(Currency::class, ['id' => 2, 'name' => 'USD', 'ration' => 100]);
    }

    public function _before()
    {
        $service = $this->make(CurrencyService::class, [
            'getCurrencyRate' => function (Currency $currency) {
                $rate = self::RATES[$currency->getName()] ?? null;
                return $this->make(CurrencyRate::class, [
                    'getValue' => $rate,
                ]);
            },
            'getRoot' => $this->usd,
        ]);
        $this->converter = new MoneyConverter($service);
    }


    public function providerConverter()
    {
        return [
            [$this->rub, new Money($this->rub, 100), new Money($this->rub, 100)],
            [$this->rub, new Money($this->usd, 100), new Money($this->rub, self::RUB_RATE * 100)],
            [$this->rub, new Money($this->usd, 75), new Money($this->rub, self::RUB_RATE * 75)],

            [$this->usd, new Money($this->usd, 150), new Money($this->usd, 150)],
            [$this->usd, new Money($this->usd, 100), new Money($this->usd, 100)],

            [$this->usd, new Money($this->rub, self::RUB_RATE * 100), new Money($this->usd, 100)],
            [$this->usd, new Money($this->rub, self::RUB_RATE * 75), new Money($this->usd, 75)],
        ];
    }

    /**
     * @param Currency $currency
     * @param Money $money
     * @param Money $result
     * @throws \App\Exception\DomainException
     * @dataProvider providerConverter
     */
    public function testConvert(Currency $currency, Money $money, Money $result)
    {
        $convertMoney = $this->converter->convert($currency, $money);
        $this->assertEquals($result->getCurrency()->getId(), $convertMoney->getCurrency()->getId());
        $this->assertEquals($result->getValue(), $convertMoney->getValue());
    }
}
