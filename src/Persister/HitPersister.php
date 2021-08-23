<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;

final class HitPersister implements HitPersisterInterface
{
    use ORMManagerTrait;

    private PropertyProviderInterface $propertyProvider;

    public function __construct(ManagerRegistry $managerRegistry, PropertyProviderInterface $propertyProvider)
    {
        $this->managerRegistry = $managerRegistry;
        $this->propertyProvider = $propertyProvider;
    }

    public function persistBuilder(HitBuilderInterface $hitBuilder): void
    {
        $manager = $this->getManager(Hit::class);

        foreach ($this->propertyProvider->getProperties() as $property) {
            $manager->persist(Hit::createFromHitBuilder($hitBuilder, $property));
        }

        $manager->flush();
    }
}
