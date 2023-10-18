<?php

namespace App\Stonks\Parser;

use App\Entity\ExchangeRate;
use App\Stonks\Collection\ExchangeRatesCollection;
use App\Stonks\Exception\ParserException;
use DateTimeImmutable;

class EcbParser extends AbstractParser
{
    public function parse(string $data): ExchangeRatesCollection
    {
        $xml = simplexml_load_string($data);
        if (!$xml) {
            throw new ParserException('invalid xml');
        }

        $json = json_decode(json_encode($xml), true);
        if (json_last_error()) {
            throw new ParserException('invalid json after converting from xml');
        }

        if (!isset($json['Cube']['Cube']['Cube'])) {
            throw new ParserException('invalid data');
        }

        $collection = new ExchangeRatesCollection;
        foreach ($json['Cube']['Cube']['Cube'] as $item) {
            $attributes = $item['@attributes'];

            if (!isset($attributes['currency']) || !isset($attributes['rate'])) {
                throw new ParserException('invalid item in data');
            }

            $exchangeRate = new ExchangeRate;
            $exchangeRate->setBaseCurrency('EUR');
            $exchangeRate->setCurrency($attributes['currency']);
            $exchangeRate->setRate(floatval($attributes['rate']));
            $exchangeRate->setCreatedAt(new DateTimeImmutable);

            $collection->add($exchangeRate);
        }

        return $collection;
    }
}