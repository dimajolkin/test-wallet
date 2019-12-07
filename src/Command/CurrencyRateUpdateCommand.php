<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\CurrencyRate;
use App\Repository\CurrencyRateRepository;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CurrencyRateUpdateCommand extends Command
{
    const API_URL = 'https://api.exchangeratesapi.io/latest';

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
        parent::__construct();
        $this->currencyRepository = $currencyRepository;
        $this->currencyRateRepository = $currencyRateRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    private function insert(Currency $currency, $value): CurrencyRate
    {
        $rate = new CurrencyRate();
        $rate->setCurrencyId($currency->getId());
        $rate->setValue($value);
        $rate->setDateCreate(new \DateTime('now'));

        return $rate;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = $this->currencyRepository->getRoot();
        $currencies = $this->currencyRepository->findAllGroupByName();
        unset($currencies[$root->getName()]);

        $context = file_get_contents(self::API_URL . '?' . http_build_query([
                'base' => $root->getName(),
                'symbols' => implode(',', array_keys($currencies)),
            ]));
        $response = json_decode($context, true);

        foreach ($response['rates'] as $currencyName => $value) {
            $currency = $currencies[$currencyName];
            $last = $this->currencyRateRepository->getLast($currency->getId());
            $newValue = (int) ($currency->getRation() * $value);
            if (!$last) {
                $rate = $this->insert($currency, $newValue);
                $this->entityManager->persist($rate);
            } elseif ($last->getValue() !== $newValue) {
                $rate = $this->insert($currency, $newValue);
                $this->entityManager->persist($rate);
            }
        }

        $this->entityManager->flush();

        return 0;
    }
}
