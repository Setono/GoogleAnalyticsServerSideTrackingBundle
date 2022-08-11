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

    private bool $consent;

    public function __construct(ManagerRegistry $managerRegistry, bool $consent = true)
    {
        $this->managerRegistry = $managerRegistry;
        $this->consent = $consent;
    }

    public function __invoke(PersistHit $message): void
    {
        $hit = Hit::createFromCommand($message);
        if (!$this->consent) {
            $hit->setConsentGranted(true);
        }

        $manager = $this->getManager($hit);
        $manager->persist($hit);
        $manager->flush();
    }
}
