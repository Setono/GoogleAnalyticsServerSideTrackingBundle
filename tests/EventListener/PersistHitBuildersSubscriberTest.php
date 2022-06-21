<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStack;
use Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistHitBuildersSubscriber;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Persister\HitPersisterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistHitBuildersSubscriber
 */
final class PersistHitBuildersSubscriberTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_persists(): void
    {
        $hitPersister = new class() implements HitPersisterInterface {
            public bool $called = false;

            public function persistBuilder(HitBuilderInterface $hitBuilder): void
            {
                $this->called = true;
            }
        };

        $kernel = $this->prophesize(HttpKernelInterface::class);

        $event = new ResponseEvent($kernel->reveal(), new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $hitBuilderStack = new HitBuilderStack();
        $hitBuilderStack->push(new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW));

        $subscriber = new PersistHitBuildersSubscriber($hitBuilderStack, $hitPersister);
        $subscriber->persist($event);

        self::assertTrue($hitPersister->called);
    }
}
