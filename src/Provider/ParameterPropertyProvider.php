<?php
declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Provider;

final class ParameterPropertyProvider implements PropertyProviderInterface
{
    private array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
