<?php

namespace App\Tests;

use App\Entity\ExchangeRate;

trait TestHelpers
{
    public function rate(string $from, string $to, float $rate): ExchangeRate
    {
        return (new ExchangeRate)
            ->setBaseCurrency($from)
            ->setCurrency($to)
            ->setRate($rate);
    }
}