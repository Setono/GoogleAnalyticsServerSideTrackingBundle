<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Filter;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;

final class BotFilter implements FilterInterface
{
    private BotDetectorInterface $botDetector;

    public function __construct(BotDetectorInterface $botDetector)
    {
        $this->botDetector = $botDetector;
    }

    public function filter(HitBuilderInterface $hitBuilder): bool
    {
        return !$this->botDetector->isBotRequest();
    }
}
