<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PersistPageViewHitBuilderToSessionSubscriber implements EventSubscriberInterface
{
    private HitBuilder $pageViewHitBuilder;

    public function __construct(HitBuilder $pageViewHitBuilder)
    {
        $this->pageViewHitBuilder = $pageViewHitBuilder;
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

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $statusCode = $event->getResponse()->getStatusCode();

        /**
         * We only want to store data in the page view hit builder if the response code is NOT in the 2xx range.
         * If the status code is in the 2xx range the user will see a page and we should instead save the hit
         * to the database to be able to send the hit to Google Analytics
         */
        if ($statusCode >= 200 && $statusCode < 300) {
            return;
        }

        $this->pageViewHitBuilder->store();
    }
}
