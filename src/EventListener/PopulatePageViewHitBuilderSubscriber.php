<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Builder\PersistableQueryBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Builder\RequestAwareQueryBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Builder\ResponseAwareQueryBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Setono\GoogleAnalyticsMeasurementProtocol\Response\Adapter\SymfonyResponseAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PopulatePageViewHitBuilderSubscriber implements EventSubscriberInterface
{
    private HitBuilderInterface $pageViewHitBuilder;

    public function __construct(HitBuilderInterface $pageViewHitBuilder)
    {
        $this->pageViewHitBuilder = $pageViewHitBuilder;
    }

    public static function getSubscribedEvents(): array
    {
        // todo validate priorities
        return [
            KernelEvents::REQUEST => ['populateFromRequestAndRestore', 1000],
            KernelEvents::RESPONSE => ['populateFromResponse', 1000],
        ];
    }

    public function populateFromRequestAndRestore(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->pageViewHitBuilder instanceof PersistableQueryBuilderInterface) {
            $this->pageViewHitBuilder->restore();
        }

        if ($this->pageViewHitBuilder instanceof RequestAwareQueryBuilderInterface) {
            $this->pageViewHitBuilder->populateFromRequest(new SymfonyRequestAdapter($event->getRequest()));
        }
    }

    public function populateFromResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->pageViewHitBuilder instanceof ResponseAwareQueryBuilderInterface) {
            return;
        }

        $this->pageViewHitBuilder->populateFromResponse(new SymfonyResponseAdapter($event->getResponse()));
    }
}
