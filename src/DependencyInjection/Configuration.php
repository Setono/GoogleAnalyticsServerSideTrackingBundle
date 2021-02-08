<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_google_analytics_server_side_tracking');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('property')
            ->children()
                ->arrayNode('properties')
                    ->info('A list of Google Analytics properties to track')
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
