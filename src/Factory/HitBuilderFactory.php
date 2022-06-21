<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderStackInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\Adapter\SymfonyRequestAdapter;
use Setono\GoogleAnalyticsMeasurementProtocol\Request\LanguageResolverInterface;
use Setono\MainRequestTrait\MainRequestTrait;
use Symfony\Component\HttpFoundation\RequestStack;

final class HitBuilderFactory implements HitBuilderFactoryInterface
{
    use MainRequestTrait;

    private RequestStack $requestStack;

    private ClientIdProviderInterface $clientIdProvider;

    private HitBuilderStackInterface $hitBuilderStack;

    private LanguageResolverInterface $languageResolver;

    public function __construct(
        RequestStack $requestStack,
        ClientIdProviderInterface $clientIdProvider,
        HitBuilderStackInterface $hitBuilderStack,
        LanguageResolverInterface $languageResolver
    ) {
        $this->requestStack = $requestStack;
        $this->clientIdProvider = $clientIdProvider;
        $this->hitBuilderStack = $hitBuilderStack;
        $this->languageResolver = $languageResolver;
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

        $request = $this->getMainRequestFromRequestStack($this->requestStack);
        if (null !== $request) {
            $hitBuilder->populateFromRequest(new SymfonyRequestAdapter($request, $this->languageResolver));
        }

        $hitBuilder->setClientId($this->clientIdProvider->getClientId()->toString());

        return $hitBuilder;
    }
}
