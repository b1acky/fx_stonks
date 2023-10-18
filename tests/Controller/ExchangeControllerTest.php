<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class ExchangeControllerTest extends WebTestCase
{
    public function testFormChoices()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $options = $crawler->filter('#exchange_calculator_from option')->each(fn(Crawler $c) => $c->attr('value'));

        $this->assertEquals([AppFixtures::CURRENCY_A, AppFixtures::CURRENCY_B], $options);
    }

    public function testFormSubmit()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $crawler = $client->submitForm('Submit', [
            'exchange_calculator[from]'   => AppFixtures::CURRENCY_A,
            'exchange_calculator[to]'     => AppFixtures::CURRENCY_B,
            'exchange_calculator[amount]' => 2.5,
        ]);

        $this->assertStringContainsString(
            sprintf('%f %s = %f %s', 2.5, AppFixtures::CURRENCY_A, 25, AppFixtures::CURRENCY_B),
            $crawler->html()
        );

        $this->assertResponseIsSuccessful();
    }
}