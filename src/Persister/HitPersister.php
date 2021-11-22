<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Setono\Consent\Context\ConsentContextInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\FilterInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command\PersistHit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class HitPersister implements HitPersisterInterface
{
    private MessageBusInterface $messageBus;

    private PropertyProviderInterface $propertyProvider;

    private ConsentContextInterface $consentContext;

    private FilterInterface $filter;

    public function __construct(
        MessageBusInterface $messageBus,
        PropertyProviderInterface $propertyProvider,
        ConsentContextInterface $consentContext,
        FilterInterface $filter
    ) {
        $this->messageBus = $messageBus;
        $this->propertyProvider = $propertyProvider;
        $this->consentContext = $consentContext;
        $this->filter = $filter;
    }

    public function persistBuilder(HitBuilderInterface $hitBuilder): void
    {
        if (!$this->filter->filter($hitBuilder)) {
            return;
        }

        foreach ($this->propertyProvider->getProperties() as $property) {
            $hit = $hitBuilder->getHit($property);

            $this->messageBus->dispatch(
                new PersistHit($hit->getPayload(), $hit->getClientId(), $this->consentContext->getConsent())
            );
        }
    }
}
