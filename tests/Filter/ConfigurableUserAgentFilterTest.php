<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\ConfigurableUserAgentFilter;

final class ConfigurableUserAgentFilterTest extends TestCase
{
    /**
     * @test
     */
    public function it_filters_configured_user_agents(): void
    {
        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);
        $hitBuilder->setUserAgentOverride('a_bot');

        $filter = new ConfigurableUserAgentFilter(['a_bot']);
        self::assertFalse($filter->filter($hitBuilder));
    }

    /**
     * @test
     */
    public function it_does_not_filter_non_matching_user_agents(): void
    {
        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);
        $hitBuilder->setUserAgentOverride('Chrome');

        $filter = new ConfigurableUserAgentFilter(['a_bot']);
        self::assertTrue($filter->filter($hitBuilder));
    }

    /**
     * @test
     */
    public function it_does_not_filter_empty_user_agent(): void
    {
        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);

        $filter = new ConfigurableUserAgentFilter(['a_bot']);
        self::assertTrue($filter->filter($hitBuilder));
    }
}
