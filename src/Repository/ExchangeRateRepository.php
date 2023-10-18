<?php

namespace App\Repository;

use App\Entity\ExchangeRate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExchangeRate>
 *
 * @method ExchangeRate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExchangeRate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExchangeRate[]    findAll()
 * @method ExchangeRate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExchangeRateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeRate::class);
    }

    public function findLatestRatesByCurrencies(string $base, string $currency): ?ExchangeRate
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.base_currency = :base')
            ->setParameter('base', $base)
            ->andWhere('r.currency = :currency')
            ->setParameter('currency', $currency)
            ->orderBy('r.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return string[]
     */
    public function getAvailableExchangeCurrencies(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.base_currency as base')
            ->distinct()
            ->addSelect('r.currency as to')
            ->groupBy('base, to')
            ->getQuery()
            ->getResult();

        return array_unique(
            array_merge(
                array_column($result, 'base'),
                array_column($result, 'to'),
            )
        );
    }
}
