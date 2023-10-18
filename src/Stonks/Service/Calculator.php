<?php

namespace App\Stonks\Service;

use App\Repository\ExchangeRateRepository;
use App\Stonks\Exception\RateNotFoundException;

class Calculator
{
    public function __construct(
        private readonly ExchangeRateRepository $exchangeRateRepository,
        private readonly ?string $mediatorCurrency = null
    ) {
    }

    /**
     * @throws RateNotFoundException
     */
    public function exchange(string $from, string $to, float $amount): float
    {
        return $amount * $this->getRate($from, $to);
    }

    /**
     * @throws RateNotFoundException
     */
    private function getRate(string $from, string $to): float
    {
        // прямой курс обмена
        $exchangeRate = $this->exchangeRateRepository->findLatestRatesByCurrencies($from, $to);
        if (!is_null($exchangeRate)) {
            return $exchangeRate->getRate();
        }

        // обратный курс обмена
        $exchangeRate = $this->exchangeRateRepository->findLatestRatesByCurrencies($to, $from);
        if (is_null($exchangeRate)) {
            if (is_null($this->mediatorCurrency) || $to === $this->mediatorCurrency || $from === $this->mediatorCurrency) {
                // если ничего не найдено и мы дошли до поиска курса через медиатор
                throw new RateNotFoundException;
            }

            return $this->getRateThroughMediator($from, $to);
        }

        return 1 / $exchangeRate->getRate();
    }

    private function getRateThroughMediator(string $from, string $to): float
    {
        $rateToMediator   = $this->getRate($from, $this->mediatorCurrency);
        $rateFromMediator = $this->getRate($this->mediatorCurrency, $to);

        return $rateFromMediator * $rateToMediator;
    }
}