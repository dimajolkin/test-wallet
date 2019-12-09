<?php

namespace App\Service\CurrencyService;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Exception\DomainException;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use App\Service\CurrencyService\RateApi\Rate;
use App\Service\CurrencyService\RateApi\RateApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class CurrencyService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;
    /**
     * @var CurrencyRateRepository
     */
    private $currencyRateRepository;

    /**
     * CurrencyService constructor.
     * @param EntityManagerInterface $entityManager
     * @param CurrencyRepository $currencyRepository
     * @param CurrencyRateRepository $currencyRateRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CurrencyRepository $currencyRepository,
        CurrencyRateRepository $currencyRateRepository
    ) {
        $this->entityManager = $entityManager;
        $this->currencyRepository = $currencyRepository;
        $this->currencyRateRepository = $currencyRateRepository;
    }

    public function getCurrencyRate(Currency $currency): CurrencyRate
    {
        $rate = $this->currencyRateRepository->getLast($currency);
        if ($rate === null) {
            throw new DomainException("Currency rate not found, please run ./bin/console app:currency-rate-update");
        }
        return $rate;
    }

    public function getRootRate(): CurrencyRate
    {
        return  $this->getCurrencyRate($this->getRoot());
    }

    public function getByName(string $name): ?Currency
    {
        return $this->currencyRepository->getByName($name);
    }

    public function getRoot(): ?Currency
    {
        return $this->currencyRepository->getRoot();
    }

    public function throwCurrencyNotFound(string $name)
    {
        throw new ValidatorException("$name not found");
    }

    public function getCurrency(?string $name, Currency $root = null): Currency
    {
        $defaultCurrency = $root !== null ? $root : $this->getRoot();

        $currency = $name !== null
            ? $this->getByName($name)
            : $defaultCurrency;
        if ($currency === null) {
            $this->throwCurrencyNotFound($name);
        }
        return $currency;
    }

    private function insert(Rate $rate): CurrencyRate
    {
        $entity = new CurrencyRate();
        $entity->setCurrencyId($rate->getCurrency()->getId());
        $entity->setValue($rate->getValue());
        $entity->setDateCreate(new \DateTime('now'));

        return $entity;
    }

    protected function getRateApi():RateApi
    {
        return new RateApi();
    }

    public function updateRate(): void
    {
        $root = $this->currencyRepository->getRoot();
        $api = $this->getRateApi();
        foreach ($api->getRates($root, $this->currencyRepository) as $rate) {
            $last = $this->currencyRateRepository->getLast($rate->getCurrency());
            if ($last === null || ($last->getValue() !== $rate->getValue())) {
                $rate = $this->insert($rate);
                $this->entityManager->persist($rate);
            }
        }

        $this->entityManager->flush();
    }
}
