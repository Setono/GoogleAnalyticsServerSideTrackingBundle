<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;

final class HitPersister implements HitPersisterInterface
{
    private ManagerRegistry $managerRegistry;

    private PropertyProviderInterface $propertyProvider;

    public function __construct(ManagerRegistry $managerRegistry, PropertyProviderInterface $propertyProvider)
    {
        $this->managerRegistry = $managerRegistry;
        $this->propertyProvider = $propertyProvider;
    }

    public function persistBuilder(HitBuilderInterface $hitBuilder): void
    {
        $manager = $this->managerRegistry->getManagerForClass(Hit::class);
        if (null === $manager) {
            throw new \LogicException(sprintf('No object manager associated with class %s', Hit::class));
        }

        foreach ($this->propertyProvider->getProperties() as $property) {
            $manager->persist(Hit::createFromHitBuilder($hitBuilder, $property));
        }

        $manager->flush();
    }
}
