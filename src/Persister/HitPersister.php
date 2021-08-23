<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Consent\Context\ConsentContextInterface;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;

final class HitPersister implements HitPersisterInterface
{
    use ORMManagerTrait;

    private PropertyProviderInterface $propertyProvider;
    private ConsentContextInterface $consentContext;

    public function __construct(
        ManagerRegistry $managerRegistry,
        PropertyProviderInterface $propertyProvider,
        ConsentContextInterface $consentContext
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->propertyProvider = $propertyProvider;
        $this->consentContext = $consentContext;
    }

    public function persistBuilder(HitBuilderInterface $hitBuilder): void
    {
        $manager = $this->getManager(Hit::class);

        foreach ($this->propertyProvider->getProperties() as $property) {
            $hit = Hit::createFromHitBuilder($hitBuilder, $property);
            $hit->setConsentGranted($this->consentContext->getConsent()->isStatisticsConsentGranted());
            $manager->persist($hit);
        }

        $manager->flush();
    }
}
