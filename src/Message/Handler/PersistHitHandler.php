<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Handler;

use Doctrine\Persistence\ManagerRegistry;
use Setono\DoctrineObjectManagerTrait\ORM\ORMManagerTrait;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\Hit;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command\PersistHit;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PersistHitHandler implements MessageHandlerInterface
{
    use ORMManagerTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(PersistHit $message): void
    {
        $hit = Hit::createFromCommand($message);
        $manager = $this->getManager($hit);
        $manager->persist($hit);
        $manager->flush();
    }
}
