<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Factory;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Storage\StorageInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;

final class HitBuilderFactory implements HitBuilderFactoryInterface
{
    private PropertyProviderInterface $propertyProvider;

    private StorageInterface $storage;

    private string $storageKey;

    public function __construct(PropertyProviderInterface $propertyProvider, StorageInterface $storage, string $storageKey)
    {
        $this->propertyProvider = $propertyProvider;
        $this->storage = $storage;
        $this->storageKey = $storageKey;
    }

    public function create(): HitBuilder
    {
        $hitBuilder = new HitBuilder($this->propertyProvider->getProperties());
        $hitBuilder->setStorage($this->storage, $this->storageKey);

        return $hitBuilder;
    }
}
