<?php

namespace App\Stonks\Service;

use App\Stonks\Parser\ParserInterface;
use App\Stonks\Utils\CurlRequester;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;

class RateImporter
{
    public function __construct(
        private readonly CurlRequester $curl,
        private readonly EntityManagerInterface $entityManager,
        #[TaggedLocator('parser.source', 'key')]
        private readonly ContainerInterface $locator,
    ) {
    }

    public function importFrom(string $source): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->locator->get($source);

        $raw = $this->curl->exec($parser->getUrl(), $parser->getTimeout());

        $ratesCollection = $parser->parse($raw);

        foreach ($ratesCollection->getAll() as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }
}