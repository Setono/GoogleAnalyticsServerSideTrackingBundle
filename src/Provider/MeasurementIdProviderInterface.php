<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Provider;

interface MeasurementIdProviderInterface
{
    /**
     * Returns a list of Google Analytics measurement ids, i.e.
     * - G-6PDK0YR1J7
     * - G-8ODL0XR1J2
     * - etc...
     *
     * @return string[]
     */
    public function getMeasurementIds(): array;
}
