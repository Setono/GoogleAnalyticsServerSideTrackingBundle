<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStackInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Persister\HitPersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PersistHitBuildersSubscriber implements EventSubscriberInterface
{
    private HitBuilderStackInterface $hitBuilderStack;

    private HitPersisterInterface $hitPersister;

    public function __construct(HitBuilderStackInterface $hitBuilderStack, HitPersisterInterface $hitPersister)
    {
        $this->hitBuilderStack = $hitBuilderStack;
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

        foreach ($this->hitBuilderStack->all(static fn (HitBuilderInterface $hitBuilder) => $hitBuilder->getHitType() !== HitBuilderInterface::HIT_TYPE_PAGEVIEW) as $hitBuilder) {
            $this->hitPersister->persistBuilder($hitBuilder);
        }

        /**
         * Here we try to mimic how the client side Google Analytics implementation would track page views
         * We track them on 2xx, 4xx, and 5xx HTTP status code
         */
        $statusCode = $event->getResponse()->getStatusCode();
        $significantStatusCode = (int) ($statusCode / 100);

        if (!in_array($significantStatusCode, [2, 4, 5], true)) {
            return;
        }

        foreach ($this->hitBuilderStack->pageviews() as $hitBuilder) {
            $this->hitPersister->persistBuilder($hitBuilder);
        }
    }
}