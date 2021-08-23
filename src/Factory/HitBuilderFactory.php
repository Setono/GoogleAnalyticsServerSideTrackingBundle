<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Symfony\Component\HttpFoundation\RequestStack;

// todo add the hit builder to the hit builder stack immediately?
final class HitBuilderFactory implements HitBuilderFactoryInterface
{
    private RequestStack $requestStack;

    private ClientIdProviderInterface $clientIdProvider;

    public function __construct(RequestStack $requestStack, ClientIdProviderInterface $clientIdProvider)
    {
        $this->requestStack = $requestStack;
        $this->clientIdProvider = $clientIdProvider;
    }

    public function createPageViewHitBuilder(): HitBuilderInterface
    {
        return $this->populateAndReturn(new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW));
    }

    public function createEventHitBuilder(): HitBuilderInterface
    {
        return $this->populateAndReturn(new HitBuilder(HitBuilderInterface::HIT_TYPE_EVENT));
    }

    private function populateAndReturn(HitBuilderInterface $hitBuilder): HitBuilderInterface
    {
        $request = $this->requestStack->getMasterRequest();
        if (null !== $request) {
            $hitBuilder->populateFromRequest(new SymfonyRequestAdapter($request));
        }

        $hitBuilder->setClientId($this->clientIdProvider->getClientId()->toString());

        return $hitBuilder;
    }
}
