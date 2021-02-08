<?php
declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Persister;

use Doctrine\Persistence\ManagerRegistry;
use Setono\GoogleAnalyticsMeasurementProtocol\Builder\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Safe\sprintf;

final class HitPersister implements HitPersisterInterface
{
    private ManagerRegistry $managerRegistry;

    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $managerRegistry, ValidatorInterface $validator) {

        $this->managerRegistry = $managerRegistry;
        $this->validator = $validator;
    }
    public function persistBuilder(HitBuilderInterface $builder): void
    {
        if($this->validator->validate($builder)->count() > 0) {
            return;
        }

        $hitValueObject = $builder->getHit();

        $hit = new Hit();
        $hit->setQuery($hitValueObject->getQuery());
        $hit->setClientId($hitValueObject->getClientId());

        $manager = $this->managerRegistry->getManagerForClass($hit);
        if(null === $manager) {
            throw new \LogicException(sprintf('No object manager associated with class %s', Hit::class));
        }

        $manager->persist($hit);
        $manager->flush();
    }
}
