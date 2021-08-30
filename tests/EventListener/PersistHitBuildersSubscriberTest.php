<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStack;
use Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistHitBuildersSubscriber;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Persister\HitPersisterInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistHitBuildersSubscriber
 */
final class PersistHitBuildersSubscriberTest extends TestCase
{
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

        $kernel = new class() extends Kernel {
            use MicroKernelTrait;

            public function __construct()
            {
                parent::__construct('test', true);
            }

            public function registerBundles()
            {
                return [];
            }

            protected function configureRoutes(RouteCollectionBuilder $routes)
            {
            }

            protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
            {
            }
        };

        $event = new ResponseEvent($kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response());

        $hitBuilderStack = new HitBuilderStack();
        $hitBuilderStack->push(new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW));

        $subscriber = new PersistHitBuildersSubscriber($hitBuilderStack, $hitPersister);
        $subscriber->persist($event);

        self::assertTrue($hitPersister->called);
    }
}
