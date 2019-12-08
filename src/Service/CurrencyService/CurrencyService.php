<?php

namespace App\Service\CurrencyService;

use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use App\Service\CurrencyService\RateApi\Rate;
use App\Service\CurrencyService\RateApi\RateApi;
use Doctrine\ORM\EntityManagerInterface;

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


    private function insert(Rate $rate): CurrencyRate
    {
        $entity = new CurrencyRate();
        $entity->setCurrencyId($rate->getCurrency()->getId());
        $entity->setValue($rate->getValue());
        $entity->setDateCreate(new \DateTime('now'));

        return $entity;
    }

    public function updateRate(): void
    {
        $root = $this->currencyRepository->getRoot();
        $api = new RateApi();
        foreach ($api->getRates($root, $this->currencyRepository) as $rate) {
            $last = $this->currencyRateRepository->getLast($rate->getCurrency()->getId());
            if ($last === null || ($last->getValue() !== $rate->getValue())) {
                $rate = $this->insert($rate);
                $this->entityManager->persist($rate);
            }
        }

        $this->entityManager->flush();
    }
}
