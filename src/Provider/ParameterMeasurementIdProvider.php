<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Provider;

final class ParameterMeasurementIdProvider implements MeasurementIdProviderInterface
{
    /** @var string[] */
    private array $measurementIds;

    /**
     * @param string[] $measurementIds
     */
    public function __construct(array $measurementIds)
    {
        $this->measurementIds = $measurementIds;
    }

    public function getMeasurementIds(): array
    {
        return $this->measurementIds;
    }
}
