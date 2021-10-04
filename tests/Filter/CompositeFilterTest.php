<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\CompositeFilter;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\FilterInterface;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\CompositeFilter
 */
final class CompositeFilterTest extends TestCase
{
    /**
     * @test
     */
    public function it_filters(): void
    {
        $filter1 = new class() implements FilterInterface {
            public function filter(HitBuilderInterface $hitBuilder): bool
            {
                return true;
            }
        };

        $filter2 = new class() implements FilterInterface {
            public function filter(HitBuilderInterface $hitBuilder): bool
            {
                return false;
            }
        };

        $compositeFilter = new CompositeFilter();
        $compositeFilter->add($filter1);
        $compositeFilter->add($filter2);

        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);

        self::assertFalse($compositeFilter->filter($hitBuilder));
    }
}
