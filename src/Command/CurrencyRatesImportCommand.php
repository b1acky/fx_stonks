<?php

namespace App\Command;

use App\Stonks\Service\RateImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:currency-rates-import')]
class CurrencyRatesImportCommand extends Command
{
    private const SOURCE_ARG = 'source';

    public function __construct(
        private readonly RateImporter $rateImporter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::SOURCE_ARG,
            InputArgument::REQUIRED,
            'Source to parse rates from ("ecb", "coindesk", etc.)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getArgument(self::SOURCE_ARG);

        $this->rateImporter->importFrom($source);

        return Command::SUCCESS;
    }
}