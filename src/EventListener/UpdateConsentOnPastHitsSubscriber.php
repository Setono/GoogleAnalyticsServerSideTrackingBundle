<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\EventListener;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\Consent\Event\ConsentUpdated;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UpdateConsentOnPastHitsSubscriber implements EventSubscriberInterface
{
    private HitRepositoryInterface $hitRepository;

    private ClientIdProviderInterface $clientIdProvider;

    public function __construct(HitRepositoryInterface $hitRepository, ClientIdProviderInterface $clientIdProvider)
    {
        $this->hitRepository = $hitRepository;
        $this->clientIdProvider = $clientIdProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsentUpdated::class => 'update',
        ];
    }

    public function update(ConsentUpdated $event): void
    {
        $this->hitRepository->updateConsentOnClientId($this->clientIdProvider->getClientId(), $event->newConsent);
    }
}
