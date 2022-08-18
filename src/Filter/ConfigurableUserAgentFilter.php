<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

final class ConfigurableUserAgentFilter implements FilterInterface
{
    /** @var list<string> */
    private array $userAgents;

    /**
     * @param list<string> $userAgents
     */
    public function __construct(array $userAgents)
    {
        $this->userAgents = $userAgents;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        if ([] === $this->userAgents) {
            return true;
        }

        $ua = $hitBuilder->getUserAgentOverride();
        if (null === $ua) {
            // the case for a user agent that's empty is handled by \Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\EmptyUserAgentFilter
            return true;
        }

        return preg_match('#' . implode('|', $this->userAgents) . '#', $ua) !== 1;
    }
}
