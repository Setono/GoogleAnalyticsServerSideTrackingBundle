<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStackInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Symfony\Component\HttpFoundation\RequestStack;

final class HitBuilderFactory implements HitBuilderFactoryInterface
{
    private RequestStack $requestStack;

    private ClientIdProviderInterface $clientIdProvider;

    private HitBuilderStackInterface $hitBuilderStack;

    public function __construct(
        RequestStack $requestStack,
        ClientIdProviderInterface $clientIdProvider,
        HitBuilderStackInterface $hitBuilderStack
    ) {
        $this->requestStack = $requestStack;
        $this->clientIdProvider = $clientIdProvider;
        $this->hitBuilderStack = $hitBuilderStack;
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
        $this->hitBuilderStack->push($hitBuilder);

        $request = $this->requestStack->getMasterRequest();
        if (null !== $request) {
            $hitBuilder->populateFromRequest(new SymfonyRequestAdapter($request));
        }

        $hitBuilder->setClientId($this->clientIdProvider->getClientId()->toString());

        return $hitBuilder;
    }
}
