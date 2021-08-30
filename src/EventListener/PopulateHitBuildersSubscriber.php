<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStackInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Response\Adapter\SymfonyResponseAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PopulateHitBuildersSubscriber implements EventSubscriberInterface
{
    private HitBuilderStackInterface $hitBuilderStack;

    public function __construct(HitBuilderStackInterface $hitBuilderStack)
    {
        $this->hitBuilderStack = $hitBuilderStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['populateFromResponse', 128],
        ];
    }

    public function populateFromResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        foreach ($this->hitBuilderStack as $hitBuilder) {
            $hitBuilder->populateFromResponse(new SymfonyResponseAdapter($event->getResponse()));
        }
    }
}
