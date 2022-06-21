<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Handler;

use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command\OverrideDocumentLocationUrl;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class OverrideDocumentLocationUrlHandler implements MessageHandlerInterface
{
    private HitBuilderInterface $hitBuilder;

    public function __construct(HitBuilderInterface $hitBuilder)
    {
        $this->hitBuilder = $hitBuilder;
    }

    public function __invoke(OverrideDocumentLocationUrl $message): void
    {
        $this->hitBuilder->setDocumentLocationUrl($message->url);
    }
}
