<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection;

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
        /** @psalm-suppress PossiblyNullArgument */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('setono_google_analytics_server_side_tracking.properties', $config['properties']);
        $container->setParameter('setono_google_analytics_server_side_tracking.send_delay', $config['send_delay']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => SendHitWorkflow::getConfig(),
        ]);
    }
}
