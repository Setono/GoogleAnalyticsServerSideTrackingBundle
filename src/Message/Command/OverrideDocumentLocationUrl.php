<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command;

/**
 * This command is not intended to be handled async
 */
final class OverrideDocumentLocationUrl
{
    /** @readonly */
    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
