<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    return $config
        ->addNamedFilter(NamedFilter::fromString('gedmo/doctrine-extensions'))
        ->addNamedFilter(NamedFilter::fromString('nyholm/psr7'))
        ->addNamedFilter(NamedFilter::fromString('setono/client-id-bundle'))
        ->addNamedFilter(NamedFilter::fromString('setono/consent-bundle'))
        ->addNamedFilter(NamedFilter::fromString('symfony/http-client'))
    ;
};
