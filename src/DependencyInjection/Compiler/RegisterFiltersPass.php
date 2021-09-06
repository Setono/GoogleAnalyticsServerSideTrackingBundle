<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('setono_google_analytics_server_side_tracking.filter.composite')) {
            return;
        }

        $filter = $container->getDefinition('setono_google_analytics_server_side_tracking.filter.composite');

        /** @var string $id */
        foreach (array_keys($container->findTaggedServiceIds('setono_google_analytics_server_side_tracking.filter')) as $id) {
            $filter->addMethodCall('add', [new Reference($id)]);
        }
    }
}
