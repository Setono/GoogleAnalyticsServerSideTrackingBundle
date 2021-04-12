<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

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

        // todo should it be that page view hits are only saved when status code is HTTP 200/404/500?
        // todo but all other hit types should be saved no matter what?
//        $statusCode = $event->getResponse()->getStatusCode();
//
//        if ($statusCode < 200 || $statusCode >= 300) {
//            return;
//        }

        foreach ($this->hitBuilderStack->all() as $hitBuilder) {
            $this->hitPersister->persistBuilder($hitBuilder);
        }
    }
}
