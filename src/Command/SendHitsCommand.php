<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Setono\GoogleAnalyticsMeasurementProtocol\Client\ClientInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\HitRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SendHitsCommand extends Command
{
    protected static $defaultName = 'setono:google-analytics:send-hits';

    private ClientInterface $client;

    private HitRepositoryInterface $hitRepository;

    private int $delay;

    public function __construct(ClientInterface $client, HitRepositoryInterface $hitRepository, int $delay)
    {
        parent::__construct();

        $this->client = $client;
        $this->hitRepository = $hitRepository;
        $this->delay = $delay;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hits = $this->hitRepository->findConsentedWithDelay($this->delay);

        foreach ($hits as $hit) {
            $this->client->sendHit((string) $hit->getQuery());
        }

        return 0;
    }
}
