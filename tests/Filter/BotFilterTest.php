<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\BotFilter;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\BotFilter
 */
final class BotFilterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_filters(): void
    {
        $botDetector = $this->prophesize(BotDetectorInterface::class);
        $botDetector->isBotRequest(null)->willReturn(true);

        $filter = new BotFilter($botDetector->reveal());

        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);

        self::assertFalse($filter->filter($hitBuilder));
    }
}
