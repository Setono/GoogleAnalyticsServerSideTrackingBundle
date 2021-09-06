<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use Setono\Consent\Context\ConsentContextInterface;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\FilterInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface;

final class HitPersister implements HitPersisterInterface
{
    use ORMManagerTrait;

    private PropertyProviderInterface $propertyProvider;

    private ConsentContextInterface $consentContext;

    private FilterInterface $filter;

    public function __construct(
        ManagerRegistry $managerRegistry,
        PropertyProviderInterface $propertyProvider,
        ConsentContextInterface $consentContext,
        FilterInterface $filter
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->propertyProvider = $propertyProvider;
        $this->consentContext = $consentContext;
        $this->filter = $filter;
    }

    public function persistBuilder(HitBuilderInterface $hitBuilder): void
    {
        if (!$this->filter->filter($hitBuilder)) {
            return;
        }

        $manager = $this->getManager(Hit::class);

        foreach ($this->propertyProvider->getProperties() as $property) {
            $hit = Hit::createFromHitBuilder($hitBuilder, $property);
            $hit->setConsentGranted($this->consentContext->getConsent()->isStatisticsConsentGranted());
            $manager->persist($hit);
        }

        $manager->flush();
    }
}
