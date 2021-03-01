<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\ParameterPropertyProvider;

final class ParameterPropertyProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_properties(): void
    {
        $provider = new ParameterPropertyProvider(['UA-123-45', 'UA-678-90']);
        self::assertSame(['UA-123-45', 'UA-678-90'], $provider->getProperties());
    }
}
