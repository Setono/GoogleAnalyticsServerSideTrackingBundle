<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Storage\StorageInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistPageViewHitBuilderToSessionSubscriber;
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
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener\PersistPageViewHitBuilderToSessionSubscriber
 */
final class PersistPageViewHitBuilderToSessionSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function it_persists(): void
    {
        $storage = new class() implements StorageInterface {
            public bool $restoreCalled = false;

            public bool $storeCalled = false;

            public function store(string $key, string $data): void
            {
                $this->storeCalled = true;
            }

            public function restore(string $key): ?string
            {
                $this->restoreCalled = true;

                return $key;
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
                // TODO: Implement registerBundles() method.
            }

            protected function configureRoutes(RouteCollectionBuilder $routes)
            {
                // TODO: Implement configureRoutes() method.
            }

            protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
            {
                // TODO: Implement configureContainer() method.
            }
        };

        $event = new ResponseEvent($kernel, new Request(), HttpKernelInterface::MASTER_REQUEST, new Response('', 301));

        $hitBuilder = new HitBuilder();
        $hitBuilder->setStorage($storage, 'storage');

        $subscriber = new PersistPageViewHitBuilderToSessionSubscriber($hitBuilder);
        $subscriber->persist($event);

        self::assertTrue($storage->storeCalled);
    }
}
