<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Setono\GoogleAnalyticsMeasurementProtocol\Response\Adapter\SymfonyResponseAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PopulatePageViewHitBuilderSubscriber implements EventSubscriberInterface
{
    private HitBuilder $pageViewHitBuilder;

    public function __construct(HitBuilder $pageViewHitBuilder)
    {
        $this->pageViewHitBuilder = $pageViewHitBuilder;
    }

    public static function getSubscribedEvents(): array
    {
        /*
         * The priority needs to be lower than Symfony\Component\HttpKernel\EventListener\SessionListener
         * which is 128, but still higher than 0 so that people can listen to the request event without
         * worrying about priorities
         */
        // todo test these priorities in phpunit if possible (maybe the TagBagBundle does this?)
        return [
            KernelEvents::REQUEST => ['populateFromRequestAndRestore', 100],
            KernelEvents::RESPONSE => ['populateFromResponse', 90],
        ];
    }

    public function populateFromRequestAndRestore(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $this->pageViewHitBuilder->restore();
        $this->pageViewHitBuilder->populateFromRequest(new SymfonyRequestAdapter($request));
    }

    public function populateFromResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()) {
            return;
        }

        $this->pageViewHitBuilder->populateFromResponse(new SymfonyResponseAdapter($event->getResponse()));
    }
}
