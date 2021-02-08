<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use function Safe\sprintf;
use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilder;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;

final class HitPersister implements HitPersisterInterface
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function persistBuilder(HitBuilder $builder): void
    {
        $manager = $this->managerRegistry->getManagerForClass(Hit::class);
        if (null === $manager) {
            throw new \LogicException(sprintf('No object manager associated with class %s', Hit::class));
        }

        foreach ($builder->getHits() as $hit) {
            $obj = new Hit();
            $obj->setQuery($hit->getPayload()->getValue());
            $obj->setClientId($hit->getClientId());

            $manager->persist($obj);
        }

        $manager->flush();
    }
}
