<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

final class EmptyUserAgentFilter implements FilterInterface
{
    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        $ua = $hitBuilder->getUserAgentOverride();

        return !(null === $ua || '' === $ua);
    }
}
