<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle;

use Setono\GoogleAnalyticsServerSideTrackingBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SetonoGoogleAnalyticsServerSideTrackingBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterFiltersPass());
    }
}
