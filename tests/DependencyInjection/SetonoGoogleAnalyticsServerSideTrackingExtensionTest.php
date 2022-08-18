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
    public function it_sets_parameters(): void
    {
        $this->load([
            'filters' => [
                'user_agent' => [
                    'a_robot',
                    'also_a_robot',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('setono_google_analytics_server_side_tracking.consent.enabled', true);
        $this->assertContainerBuilderHasParameter('setono_google_analytics_server_side_tracking.properties', []);
        $this->assertContainerBuilderHasParameter('setono_google_analytics_server_side_tracking.send_delay', 300);
        $this->assertContainerBuilderHasParameter('setono_google_analytics_server_side_tracking.prune_delay', 1440);
        $this->assertContainerBuilderHasParameter('setono_google_analytics_server_side_tracking.filters.user_agent', ['a_robot', 'also_a_robot']);
    }
}
