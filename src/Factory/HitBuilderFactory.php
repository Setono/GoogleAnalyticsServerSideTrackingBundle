<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStackInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class HitBuilderFactory implements HitBuilderFactoryInterface
{
    private HitBuilderStackInterface $hitBuilderStack;

    private RequestStack $requestStack;

    private ClientIdProviderInterface $clientIdProvider;

    public function __construct(HitBuilderStackInterface $hitBuilderStack, RequestStack $requestStack, ClientIdProviderInterface $clientIdProvider)
    {
        $this->hitBuilderStack = $hitBuilderStack;
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
        $request = $this->getMasterRequest();
        if (null !== $request) {
            $hitBuilder->populateFromRequest(new SymfonyRequestAdapter($request));
        }

        $hitBuilder->setClientId($this->clientIdProvider->get()->toString());

        $this->hitBuilderStack->push($hitBuilder);

        return $hitBuilder;
    }

    private function getMasterRequest(): ?Request
    {
        return $this->requestStack->getMasterRequest();
    }
}
