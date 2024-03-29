<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection;

use Setono\GoogleAnalyticsServerSideTrackingBundle\Filter\FilterInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Workflow\SendHitWorkflow;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoGoogleAnalyticsServerSideTrackingExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{consent: array{enabled: bool}, properties: array, send_delay: int, prune_delay: int, filters: array{user_agent: list<string>}} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('setono_google_analytics_server_side_tracking.consent.enabled', $config['consent']['enabled']);
        $container->setParameter('setono_google_analytics_server_side_tracking.properties', $config['properties']);
        $container->setParameter('setono_google_analytics_server_side_tracking.send_delay', $config['send_delay']);
        $container->setParameter('setono_google_analytics_server_side_tracking.prune_delay', $config['prune_delay']);
        $container->setParameter('setono_google_analytics_server_side_tracking.filters.user_agent', $config['filters']['user_agent'] ?? []);

        $container
            ->registerForAutoconfiguration(FilterInterface::class)
            ->addTag('setono_google_analytics_server_side_tracking.filter')
        ;

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if ($container->hasParameter('kernel.debug') && $container->getParameter('kernel.debug') === true) {
            $loader->load('services/debug/filter.xml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => SendHitWorkflow::getConfig(),
            'messenger' => [
                'buses' => [
                    'setono_google_analytics_server_side_tracking.command_bus' => null,
                ],
            ],
        ]);
    }
}
