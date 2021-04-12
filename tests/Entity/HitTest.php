<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

final class HitTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_and_gets(): void
    {
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime('+1 second');

        $hit = new Hit();
        self::assertFalse($hit->isConsentGranted());
        self::assertSame(HitInterface::STATE_PENDING, $hit->getState());

        $hit->setClientId('client_id');
        $hit->setQuery('key1=value1&key2=value2');
        $hit->setConsentGranted(true);
        $hit->setState(HitInterface::STATE_SENT);
        $hit->setCreatedAt($createdAt);
        $hit->setUpdatedAt($updatedAt);

        self::assertSame('client_id', $hit->getClientId());
        self::assertSame('key1=value1&key2=value2', $hit->getQuery());
        self::assertSame(HitInterface::STATE_SENT, $hit->getState());
        self::assertTrue($hit->isConsentGranted());

        self::assertSame($createdAt->format(\DATE_ATOM), $hit->getCreatedAt()->format(\DATE_ATOM));

        $hitUpdatedAt = $hit->getUpdatedAt();
        self::assertNotNull($hitUpdatedAt);
        self::assertSame($updatedAt->format(\DATE_ATOM), $hitUpdatedAt->format(\DATE_ATOM));
    }
}
