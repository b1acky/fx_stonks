<?php

namespace App\Stonks\Parser;

use App\Stonks\Collection\ExchangeRatesCollection;

interface ParserInterface
{
    public function getUrl(): string;

    public function getTimeout(): int;

    public function parse(string $data): ExchangeRatesCollection;
}