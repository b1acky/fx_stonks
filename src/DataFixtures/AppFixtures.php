<?php

namespace App\DataFixtures;

use App\Entity\ExchangeRate;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const CURRENCY_A = 'AAA';
    public const CURRENCY_B = 'BBB';
    public const RATE_A_B = 10;

    public function load(ObjectManager $manager): void
    {
        $rate = new ExchangeRate;
        $rate->setBaseCurrency(self::CURRENCY_A);
        $rate->setCurrency(self::CURRENCY_B);
        $rate->setRate(self::RATE_A_B);
        $rate->setCreatedAt(new DateTimeImmutable);

        $manager->persist($rate);

        $manager->flush();
    }
}
