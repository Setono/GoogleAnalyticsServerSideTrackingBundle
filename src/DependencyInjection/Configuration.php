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
            ->end()
        ;

        return $treeBuilder;
    }
}
