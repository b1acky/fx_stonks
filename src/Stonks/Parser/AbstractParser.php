<?php

namespace App\Stonks\Parser;

abstract class AbstractParser implements ParserInterface
{
    public function __construct(
        private readonly string $url,
        private readonly int $timeout,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}