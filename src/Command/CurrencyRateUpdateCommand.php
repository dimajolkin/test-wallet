<?php

namespace App\Command;

use App\Service\CurrencyService\CurrencyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencyRateUpdateCommand extends Command
{
    protected static $defaultName = 'app:currency-rate-update';
    /**
     * @var CurrencyService
     */
    private $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        parent::__construct();
        $this->currencyService = $currencyService;
    }

    protected function configure(): void
    {
        $this->setDescription('Actualization currency rate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->currencyService->updateRate();

        return 0;
    }
}
