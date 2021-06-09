<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_google_analytics_server_side_tracking');

        $rootNode = $treeBuilder->getRootNode();

        /** @psalm-suppress MixedMethodCall, PossiblyNullReference, PossiblyUndefinedMethod */
        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('property')
            ->children()
                ->arrayNode('properties')
                    ->info('A list of Google Analytics properties to track. Notice that you can implement your own property provider by implementing the interface Setono\GoogleAnalyticsServerSideTrackingBundle\Provider\PropertyProviderInterface. This way you can get the properties from a database or any other source.')
                    ->scalarPrototype()
                        ->info('The Google Analytics property id')
                        ->example('UA-1234-5')
                    ->end()
                ->end()
                ->integerNode('send_delay')
                    ->defaultValue(300) // 5 minutes
                    ->info('The number of seconds to wait until a hit is sent to Google Analytics')
                    ->example(120) // 2 minutes
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
