<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\SetonoGoogleAnalyticsServerSideTrackingExtension;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\SetonoGoogleAnalyticsServerSideTrackingExtension
 */
final class SetonoGoogleAnalyticsServerSideTrackingExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SetonoGoogleAnalyticsServerSideTrackingExtension(),
        ];
    }

    /**
     * @test
     */
    public function it_can_load(): void
    {
        $this->setParameter('kernel.debug', true);

        $this->load();

        self::assertTrue(true);
    }
}
