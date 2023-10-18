<?php

namespace App\Command;

use App\Stonks\Exception\RateNotFoundException;
use App\Stonks\Service\Calculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('app:currency-exchange-calc')]
class CurrencyExchangeCalcCommand extends Command
{
    public const INTERNAL_ERROR_EXIT_CODE = 255;

    private const BASE_CURRENCY_ARG = 'base_currency';
    private const EXCHANGE_CURRENCY_ARG = 'exchange_currency';
    private const AMOUNT_ARG = 'amount';

    public function __construct(
        private readonly Calculator $calculator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(self::BASE_CURRENCY_ARG, InputArgument::REQUIRED, 'Currency to exchange from')
            ->addArgument(self::EXCHANGE_CURRENCY_ARG, InputArgument::REQUIRED, 'Currency to exchange to')
            ->addArgument(self::AMOUNT_ARG, InputArgument::REQUIRED, 'Amount of currency to exchange');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $from */
        $from = $input->getArgument(self::BASE_CURRENCY_ARG);
        /** @var string $to */
        $to     = $input->getArgument(self::EXCHANGE_CURRENCY_ARG);
        $amount = $input->getArgument(self::AMOUNT_ARG);

        if (!is_numeric($amount)) {
            $output->writeln('`amount` should be a number');

            return Command::FAILURE;
        }

        $amount = floatval($amount);

        try {
            $convertedAmount = $this->calculator->exchange($from, $to, $amount);
        } catch (RateNotFoundException) {
            $output->writeln(sprintf('no rates found for exchange %s<->%s', $from, $to));

            return Command::FAILURE;
        } catch (Throwable) {
            $output->writeln('internal error');

            return self::INTERNAL_ERROR_EXIT_CODE;
        }

        $output->writeln(sprintf("%f %s = %f %s", $amount, $from, $convertedAmount, $to));

        return Command::SUCCESS;
    }
}