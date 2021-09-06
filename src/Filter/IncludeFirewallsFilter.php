<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Use this filter to include requests only with a given array of firewalls.
 * This proves very useful is you for instance you have an ecommerce store where you have multiple firewalls,
 * one of them being 'shop' for example. Then you can use this filter to ONLY include requests within the 'shop'
 * firewall to be persisted
 */
final class IncludeFirewallsFilter implements FilterInterface
{
    private RequestStack $requestStack;

    private FirewallMap $firewallMap;

    private array $firewalls;

    /**
     * @param array $firewalls if the request is within one of these firewalls, the HitBuilder will NOT be filtered
     */
    public function __construct(RequestStack $requestStack, FirewallMap $firewallMap, array $firewalls)
    {
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
        $this->firewalls = $firewalls;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return true;
        }

        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if (null === $firewallConfig) {
            return true;
        }

        return in_array($firewallConfig->getName(), $this->firewalls, true);
    }
}
