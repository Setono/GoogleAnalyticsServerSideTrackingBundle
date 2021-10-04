<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilder;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\BotFilter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\BotFilter
 */
final class BotFilterTest extends TestCase
{
    /**
     * @test
     */
    public function it_filters(): void
    {
        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Googlebot',
        ]);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $filter = new BotFilter($requestStack, new NullAdapter());

        $hitBuilder = new HitBuilder(HitBuilderInterface::HIT_TYPE_PAGEVIEW);

        self::assertFalse($filter->filter($hitBuilder));
    }
}
