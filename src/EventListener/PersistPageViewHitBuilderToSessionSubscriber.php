<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
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
        /*
         * The priority needs to be higher than Symfony\Component\HttpKernel\EventListener\SessionListener
         * which is -1000, but still lower than 0 so that people can listen to the response event without
         * worrying about priorities
         */
        // todo test these priorities in phpunit if possible (maybe the TagBagBundle does this?)
        return [
            KernelEvents::RESPONSE => ['persist', -900],
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
