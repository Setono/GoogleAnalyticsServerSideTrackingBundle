<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Persister\HitPersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PersistPageViewHitBuilderToDatabaseSubscriber implements EventSubscriberInterface
{
    private HitBuilderInterface $pageViewHitBuilder;
    private HitPersisterInterface $hitPersister;

    public function __construct(HitBuilderInterface $pageViewHitBuilder, HitPersisterInterface $hitPersister)
    {
        $this->pageViewHitBuilder = $pageViewHitBuilder;
        $this->hitPersister = $hitPersister;
    }

    public static function getSubscribedEvents(): array
    {
        // todo validate priorities
        return [
            KernelEvents::RESPONSE => ['persist', -1000],
        ];
    }

    public function persist(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $statusCode = $event->getResponse()->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            return;
        }

        $this->hitPersister->persistBuilder($this->pageViewHitBuilder);
    }
}
