<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoGoogleAnalyticsServerSideTrackingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @psalm-suppress PossiblyNullArgument */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('setono_google_analytics_server_side_tracking.cookie_key', $config['cookie_key']);
        $container->setParameter('setono_google_analytics_server_side_tracking.measurement_ids', $config['measurement_ids']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }
}
