<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

interface FilterInterface
{
    /**
     * If the filter returns false, the given $hitBuilder should be filtered
     */
    public function filter(HitBuilderInterface $hitBuilder): bool;
}
