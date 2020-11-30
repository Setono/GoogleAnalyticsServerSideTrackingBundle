<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Repository;

use Doctrine\Persistence\ObjectRepository;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\EventInterface;

interface EventRepositoryInterface extends ObjectRepository
{
    /**
     * Will return events with consent created before $delay
     *
     * @param int $delay in seconds
     *
     * @return EventInterface[]
     */
    public function findConsentedWithDelay(int $delay): array;
}
