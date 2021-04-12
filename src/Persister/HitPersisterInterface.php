<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

interface HitPersisterInterface
{
    /**
     * Will persist a hit in the database based on a builder instance
     */
    public function persistBuilder(HitBuilderInterface $hitBuilder): void;
}
