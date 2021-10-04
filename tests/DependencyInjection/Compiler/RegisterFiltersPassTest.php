<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @covers \Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\Compiler\RegisterFiltersPass
 */
final class RegisterFiltersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_filters(): void
    {
        $this->setDefinition('setono_google_analytics_server_side_tracking.filter.composite', new Definition());

        $collectedService = new Definition();
        $collectedService->addTag('setono_google_analytics_server_side_tracking.filter');
        $this->setDefinition('collected_service', $collectedService);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'setono_google_analytics_server_side_tracking.filter.composite',
            'add',
            [
                new Reference('collected_service'),
            ]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterFiltersPass());
    }
}
