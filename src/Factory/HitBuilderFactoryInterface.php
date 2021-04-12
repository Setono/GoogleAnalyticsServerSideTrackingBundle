<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

interface HitBuilderFactoryInterface
{
    public function createPageViewHitBuilder(): HitBuilderInterface;

    public function createEventHitBuilder(): HitBuilderInterface;
}
