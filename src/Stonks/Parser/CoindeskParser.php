<?php

namespace App\Stonks\Parser;

use App\Entity\ExchangeRate;
use App\Stonks\Collection\ExchangeRatesCollection;
use App\Stonks\Exception\ParserException;
use DateTimeImmutable;

class CoindeskParser extends AbstractParser
{
    public function parse(string $data): ExchangeRatesCollection
    {
        $jsonData = json_decode($data, true);
        if (json_last_error() || !isset($jsonData['bpi'])) {
            throw new ParserException('invalid json');
        }

        $collection = new ExchangeRatesCollection;
        foreach ($jsonData['bpi'] as $currency => $rateData) {
            if (!isset($rateData['rate_float'])) {
                throw new ParserException('invalid format');
            }

            $exchangeRate = new ExchangeRate;
            $exchangeRate->setBaseCurrency('BTC');
            $exchangeRate->setCurrency($currency);
            $exchangeRate->setRate(floatval($rateData['rate_float']));
            $exchangeRate->setCreatedAt(new DateTimeImmutable);

            $collection->add($exchangeRate);
        }

        return $collection;
    }
}