<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ObjectRepository;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

interface HitRepositoryInterface extends ObjectRepository, ServiceEntityRepositoryInterface
{
    /**
     * Will return hits with consent created before $delay
     *
     * @param int $delay in seconds
     *
     * @return array<array-key, HitInterface>
     */
    public function findConsentedWithDelay(int $delay): array;
}
