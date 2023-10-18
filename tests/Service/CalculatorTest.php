<?php

namespace App\Tests\Service;

use App\Entity\ExchangeRate;
use App\Repository\ExchangeRateRepository;
use App\Stonks\Exception\RateNotFoundException;
use App\Stonks\Service\Calculator;
use App\Tests\TestHelpers;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculatorTest extends KernelTestCase
{
    use TestHelpers;

    public function testSimpleExchange()
    {
        $currency_a  = 'AAA';
        $currency_b  = 'BBB';
        $rate_a_to_b = 2;

        $exchangeRepo = $this->createMock(ExchangeRateRepository::class);

        $exchangeRepo->expects(self::once())
            ->method('findLatestRatesByCurrencies')
            ->willReturn(
                (new ExchangeRate())
                    ->setBaseCurrency($currency_a)
                    ->setCurrency($currency_b)
                    ->setRate($rate_a_to_b)
            );

        $calculator = new Calculator($exchangeRepo);

        $result = $calculator->exchange($currency_a, $currency_b, 10);

        $this->assertEquals(20, $result);
    }

    public function testReverseExchange()
    {
        $currency_a  = 'AAA';
        $currency_b  = 'BBB';
        $rate_a_to_b = 2;

        $exchangeRepo = $this->createMock(ExchangeRateRepository::class);
        $exchangeRepo->expects(self::once())
            ->method('findLatestRatesByCurrencies')
            ->willReturn($this->rate($currency_a, $currency_b, $rate_a_to_b));

        $calculator = new Calculator($exchangeRepo);

        $result = $calculator->exchange($currency_a, $currency_b, 10);

        $this->assertEquals(20, $result);
    }

    public function testExchangeThroughMediator()
    {
        $currency_a  = 'AAA';
        $currency_b  = 'BBB';
        $currency_m  = 'MMM';
        $rate_a_to_m = 2;
        $rate_m_to_b = 3;

        $exchangeRepo   = $this->createMock(ExchangeRateRepository::class);
        $invocationRule = self::exactly(4);
        $exchangeRepo->expects($invocationRule) // провкерки: AAA->BBB BBB->AAA AAA->MMM MMM->BBB
        ->method('findLatestRatesByCurrencies')
            ->willReturnCallback(fn(string $from, string $to) => match (true) {
                $from === $currency_a && $to === $currency_b => null,
                $from === $currency_b && $to === $currency_a => null,
                $from === $currency_a && $to === $currency_m => $this->rate($currency_a, $currency_m, $rate_a_to_m),
                $from === $currency_m && $to === $currency_b => $this->rate($currency_m, $currency_b, $rate_m_to_b),
            });

        $calculator = new Calculator($exchangeRepo, 'MMM');

        $result = $calculator->exchange($currency_a, $currency_b, 10);

        $this->assertEquals(60, $result);
    }

    public function testExchangeThroughMediatorWithReverse()
    {
        $currency_a  = 'AAA';
        $currency_b  = 'BBB';
        $currency_m  = 'MMM';
        $rate_a_to_m = 2;
        $rate_b_to_m = 3;

        $exchangeRepo   = $this->createMock(ExchangeRateRepository::class);
        $invocationRule = self::exactly(5);
        $exchangeRepo->expects($invocationRule) // провкерки: BBB->AAA AAA->BBB BBB->MMM MMM->BBB MMM->AAA
        ->method('findLatestRatesByCurrencies')
            ->willReturnCallback(fn(string $from, string $to) => match (true) {
                $from === $currency_a && $to === $currency_b => null,
                $from === $currency_b && $to === $currency_a => null,
                $from === $currency_b && $to === $currency_m => null,
                $from === $currency_m && $to === $currency_b => $this->rate($currency_m, $currency_b, $rate_b_to_m),
                $from === $currency_m && $to === $currency_a => $this->rate($currency_m, $currency_a, $rate_a_to_m),
            });

        $calculator = new Calculator($exchangeRepo, 'MMM');

        $result = $calculator->exchange($currency_b, $currency_a, 10);

        $this->assertEquals(10 * (1 / 3) * 2, $result);
    }

    public function testExceptionIsThrownIfRateIsNotFound()
    {
        $exchangeRepo = $this->createMock(ExchangeRateRepository::class);
        $exchangeRepo->expects(self::any())
            ->method('findLatestRatesByCurrencies')
            ->willReturn(null);

        $calculator = new Calculator($exchangeRepo);

        $this->expectException(RateNotFoundException::class);

        $calculator->exchange('a', 'b', 10);
    }
}