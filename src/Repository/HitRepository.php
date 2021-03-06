<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Repository;

use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;
use Webmozart\Assert\Assert;

class HitRepository extends ServiceEntityRepository implements HitRepositoryInterface
{
    public function hasConsentedPending(int $delay = 0): bool
    {
        $qb = $this->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.consentGranted = true')
            ->andWhere('o.state = :state')
            ->setParameter('state', HitInterface::STATE_PENDING)
        ;

        self::applyDelay($qb, $delay);

        $result = $qb->getQuery()->getSingleScalarResult();

        Assert::integerish($result);

        return (int) $result > 0;
    }

    public function assignBulkIdentifierToPendingConsented(string $bulkIdentifier, int $delay = 0, int $limit = 1000): void
    {
        Assert::greaterThan($limit, 0);

        $qb = $this->createQueryBuilder('o')
            ->update()
            ->set('o.bulkIdentifier', ':bulkIdentifier')
            ->setParameter('bulkIdentifier', $bulkIdentifier)
            ->andWhere('o.consentGranted = true')
            ->andWhere('o.state = :state')
            ->setParameter('state', HitInterface::STATE_PENDING)
            ->setMaxResults($limit)
        ;

        self::applyDelay($qb, $delay);

        $qb->getQuery()->execute();
    }

    /**
     * @return array<array-key, HitInterface>
     */
    public function findByBulkIdentifier(string $bulkIdentifier): array
    {
        $result = $this->createQueryBuilder('o')
            ->andWhere('o.bulkIdentifier = :bulkIdentifier')
            ->setParameter('bulkIdentifier', $bulkIdentifier)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($result);
        Assert::allIsInstanceOf($result, HitInterface::class);

        return $result;
    }

    private static function applyDelay(QueryBuilder $qb, int $delay): void
    {
        Assert::greaterThanEq($delay, 0);

        if ($delay > 0) {
            $then = (new DateTime())->sub(new DateInterval("PT{$delay}S"));
            $qb->andWhere('o.createdAt < :then')
                ->setParameter('then', $then)
            ;
        }
    }
}
