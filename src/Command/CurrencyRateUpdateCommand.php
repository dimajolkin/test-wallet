<?php

namespace App\Command;

use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use App\Service\CurrencyRateApi\CurrencyRateApi;
use App\Service\CurrencyRateApi\Rate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyRateUpdateCommand extends Command
{
    protected static $defaultName = 'app:currency-rate-update';
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

    public function __construct(
        EntityManagerInterface $entityManager,
        CurrencyRepository $currencyRepository,
        CurrencyRateRepository $currencyRateRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->currencyRepository = $currencyRepository;
        $this->currencyRateRepository = $currencyRateRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Actualization currency rate');
    }

    private function insert(Rate $rate): CurrencyRate
    {
        $entity = new CurrencyRate();
        $entity->setCurrencyId($rate->getCurrency()->getId());
        $entity->setValue($rate->getValue());
        $entity->setDateCreate(new \DateTime('now'));

        return $entity;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = $this->currencyRepository->getRoot();
        $api = new CurrencyRateApi();
        foreach ($api->response($root, $this->currencyRepository) as $rate) {
            $last = $this->currencyRateRepository->getLast($rate->getCurrency()->getId());
            if ($last === null || ($last->getValue() !== $rate->getValue())) {
                $rate = $this->insert($rate);
                $this->entityManager->persist($rate);
            }
        }

        $this->entityManager->flush();
        return 0;
    }
}
