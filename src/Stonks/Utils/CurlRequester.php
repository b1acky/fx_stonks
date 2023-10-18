<?php

namespace App\Stonks\Utils;

interface CurlRequester
{
    public function exec(string $url, int $timeout = 60): string;
}