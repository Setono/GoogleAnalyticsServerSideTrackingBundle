<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Repository;

use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Safe\DateTime;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

class HitRepository extends ServiceEntityRepository implements HitRepositoryInterface
{
    /**
     * @return array<array-key, HitInterface>
     */
    public function findConsentedWithDelay(int $delay): array
    {
        $then = (new DateTime())->sub(new DateInterval("PT{$delay}S"));

        return $this->createQueryBuilder('o')
            ->andWhere('o.createdAt < :then')
            ->andWhere('o.consentGranted = true')
            ->setParameter('then', $then)
            ->setMaxResults(1000) // just to avoid any memory problems
            ->getQuery()
            ->getResult()
        ;
    }
}