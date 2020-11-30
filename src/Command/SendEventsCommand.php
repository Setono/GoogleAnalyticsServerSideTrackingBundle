<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Command;

use Setono\GoogleAnalyticsMeasurementProtocol\Builder\PayloadBuilder;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\EventInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Repository\EventRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SendEventsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'setono:sylius-analytics:push-hits';

    private HttpClientInterface $httpClient;

    private EventRepositoryInterface $eventRepository;

    private int $delay;

    public function __construct(HttpClientInterface $httpClient, EventRepositoryInterface $hitRepository, int $delay)
    {
        parent::__construct();

        $this->httpClient = $httpClient;
        $this->eventRepository = $hitRepository;
        $this->delay = $delay;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $events = $this->eventRepository->findConsentedWithDelay($this->delay);

        $responses = [];

        foreach ($events as $event) {
            $payloadBuilder = new PayloadBuilder();
            $payloadBuilder->clientId = $event->getClientId();
            $payloadBuilder->events[] = $event;

            $responses[] = $this->httpClient->request('POST', 'https://www.google-analytics.com/mp/collect', [
                'body' => $payloadBuilder->toJson(),
                'user_data' => $event->getId(),
            ]);
        }

        foreach ($this->httpClient->stream($responses, 5) as $response => $chunk) {
            if (!$chunk->isLast()) {
                continue;
            }

            if ($response->getStatusCode() !== 200) {
                continue;
            }

            /** @var EventInterface|null $event */
            $event = $this->eventRepository->find($response->getInfo('user_data'));

            if (null === $event) {
                continue;
            }

            $this->eventRepository->remove($event);
        }

        return 0;
    }
}
