<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Setono\ClientId\ClientId;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\Hit as HitPayload;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

final class HitTest extends TestCase
{
    /**
     * @test
     */
    public function it_sets_and_gets(): void
    {
        $payload = new HitPayload([
            'v' => '1',
            'cid' => 'client_id',
            'tid' => 'UA-1234-5',
        ]);

        $createdAt = new \DateTime();
        $updatedAt = new \DateTime('+1 second');

        $hit = new Hit();
        self::assertFalse($hit->isConsentGranted());
        self::assertSame(HitInterface::STATE_PENDING, $hit->getState());

        $hit->setClientId(new ClientId('client_id'));
        $hit->setHit($payload);
        $hit->setConsentGranted(true);
        $hit->setState(HitInterface::STATE_SENT);
        $hit->setCreatedAt($createdAt);
        $hit->setUpdatedAt($updatedAt);

        self::assertSame('client_id', (string) $hit->getClientId());
        self::assertSame($payload, $hit->getHit());
        self::assertSame(HitInterface::STATE_SENT, $hit->getState());
        self::assertTrue($hit->isConsentGranted());

        self::assertSame($createdAt->format(\DATE_ATOM), $hit->getCreatedAt()->format(\DATE_ATOM));

        $hitUpdatedAt = $hit->getUpdatedAt();
        self::assertNotNull($hitUpdatedAt);
        self::assertSame($updatedAt->format(\DATE_ATOM), $hitUpdatedAt->format(\DATE_ATOM));
    }
}
