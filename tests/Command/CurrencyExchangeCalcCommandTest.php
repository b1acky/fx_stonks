<?php

namespace App\Tests\Command;

use App\Repository\ExchangeRateRepository;
use App\Stonks\Service\Calculator;
use App\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CurrencyExchangeCalcCommandTest extends KernelTestCase
{
    use TestHelpers;

    public function testCallCalculator()
    {
        $kernel      = self::bootKernel();
        $application = new Application($kernel);

        $container = self::getContainer();

        $calculator = $this->createMock(Calculator::class);
        $calculator->expects(self::once())
            ->method('exchange')
            ->willReturnCallback(function (string $from, string $to, float $amount) {
                $this->assertEquals('AAA', $from);
                $this->assertEquals('BBB', $to);
                $this->assertEquals(10, $amount);

                return 100;
            });

        $container->set(Calculator::class, $calculator);

        $command = $application->find('app:currency-exchange-calc');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency'     => 'AAA',
            'exchange_currency' => 'BBB',
            'amount'            => 10
        ]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(sprintf('%f %s = %f %s', 10, 'AAA', 100, 'BBB'), $output);
    }

    /**
     * @dataProvider invalidAmountDataProvider
     */
    public function testErrorWhenAmountIsNotANumber($amount)
    {
        $kernel      = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:currency-exchange-calc');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency'     => 'AAA',
            'exchange_currency' => 'BBB',
            'amount'            => $amount
        ]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testPrintExceptionWhenRateNotFound()
    {
        $kernel      = self::bootKernel();
        $application = new Application($kernel);

        $container = self::getContainer();

        $exchangeRateRepository = $this->createMock(ExchangeRateRepository::class);
        $exchangeRateRepository->expects(self::any())
            ->method('findLatestRatesByCurrencies')
            ->willReturn(null);

        $container->set(ExchangeRateRepository::class, $exchangeRateRepository);

        $command = $application->find('app:currency-exchange-calc');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'base_currency'     => 'AAA',
            'exchange_currency' => 'BBB',
            'amount'            => 10
        ]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public static function invalidAmountDataProvider(): array
    {
        return [
            ['abc'],
            [''],
            [null],
        ];
    }
}