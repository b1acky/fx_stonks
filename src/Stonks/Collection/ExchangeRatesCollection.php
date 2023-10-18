<?php

namespace App\Stonks\Collection;

use App\Entity\ExchangeRate;

class ExchangeRatesCollection
{
    /** @var ExchangeRate[] */
    private array $exchangeRates;

    public function add(ExchangeRate $exchangeRate): void
    {
        $this->exchangeRates[] = $exchangeRate;
    }

    public function getAll(): array
    {
        return $this->exchangeRates;
    }
}