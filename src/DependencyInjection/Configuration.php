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

        /**
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress PossiblyUndefinedMethod
         */
        $rootNode
            ->addDefaultsIfNotSet()
            ->fixXmlConfig('measurement_id')
            ->children()
                ->scalarNode('cookie_key')
                    ->defaultValue('sgasst_client_id') // sgasst is an acronym for 'Setono Google Analytics Server Side Tracking' :D
                    ->info('The name of the cookie where the client id is saved')
                    ->example('yourapp_client_id')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('measurement_ids')
                    ->info('A list of Google Analytics measurement ids to track')
                    ->scalarPrototype()
                        ->info('The Google Analytics measurement id')
                        ->example('G-6PDK0YR1J7')
                    ->end()
                ->end()
                ->integerNode('send_delay')
                    ->info('The number of seconds after an event entity is saved and it is sent to Google')
                    ->defaultValue(600) // 10 minutes
                    ->example(300)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
