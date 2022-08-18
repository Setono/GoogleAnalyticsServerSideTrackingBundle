<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\EmptyUserAgentFilter;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\EmptyUserAgentFilter
 */
final class EmptyUserAgentFilterTest extends TestCase
{
    /**
     * @test
     */
    public function it_filters_empty_user_agent(): void
    {
        $filter = new EmptyUserAgentFilter();
        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);

        self::assertFalse($filter->filter($hitBuilder));
    }

    /**
     * @test
     */
    public function it_does_not_filter_non_empty_user_agent(): void
    {
        $filter = new EmptyUserAgentFilter();
        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);
        $hitBuilder->setUserAgentOverride('Chrome');

        self::assertTrue($filter->filter($hitBuilder));
    }
}
