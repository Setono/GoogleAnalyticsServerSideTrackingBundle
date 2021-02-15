<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Resolver;

interface ClientIdResolverInterface
{
    /**
     * Will return a client id to use with the tracking. See https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#cid
     */
    public function resolve(): string;
}
