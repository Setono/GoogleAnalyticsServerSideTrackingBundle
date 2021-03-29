<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistPageViewHitBuilderToDatabaseSubscriber;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Persister\HitPersisterInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistPageViewHitBuilderToDatabaseSubscriber
 */
final class PersistPageViewHitBuilderToDatabaseSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function it_persists(): void
    {
        $hitPersister = new class() implements HitPersisterInterface {
            public bool $called = false;

            public function persistBuilder(HitBuilder $builder): void
            {
                $this->called = true;
            }
        };

        $kernel = new class() extends Kernel {
            use MicroKernelTrait;

            public function __construct()
            {
                parent::__construct('test', true);
            }
        };

        $event = new ResponseEvent($kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $subscriber = new PersistPageViewHitBuilderToDatabaseSubscriber(new HitBuilder(['UA-123-45']), $hitPersister);
        $subscriber->persist($event);

        self::assertTrue($hitPersister->called);
    }
}
