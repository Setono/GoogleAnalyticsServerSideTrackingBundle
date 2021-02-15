<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilder;

interface HitBuilderFactoryInterface
{
    public function create(): HitBuilder;
}
