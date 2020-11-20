<?php
declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Provider;

interface PropertyProviderInterface
{
    /**
     * Returns a list of Google Analytics property ids, i.e.
     * - UA-1234-5
     * - UA-7891-1
     * - etc...
     *
     * @return string[]
     */
    public function getProperties(): array;
}
