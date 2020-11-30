<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Resolver;

interface ClientIdResolverInterface
{
    /**
     * Will return a client id to use with the tracking. See https://developers.google.com/analytics/devguides/collection/protocol/ga4/reference
     */
    public function resolve(): string;
}
