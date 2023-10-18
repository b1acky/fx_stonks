<?php

namespace App\Stonks\Utils;

use App\Stonks\Exception\CurlExecException;
use App\Stonks\Exception\CurlInitException;
use Psr\Log\LoggerInterface;

class Curl implements CurlRequester
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function exec(string $url, int $timeout = 60): string
    {
        $curl = curl_init();
        if ($curl === false) {
            throw new CurlInitException;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $this->logger->info('curl start', compact('url', 'timeout'));

        $result = curl_exec($curl);
        if ($result === false) {
            $error = curl_error($curl);

            $this->logger->error('curl error', compact('error'));

            throw new CurlExecException(sprintf('curl %s error: %s', $url, $error));
        }

        $this->logger->info('curl result', compact('result'));

        curl_close($curl);

        return $result;
    }
}