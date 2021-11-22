<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command;

use Setono\Consent\Consent;

final class PersistHit implements CommandInterface
{
    private string $query;

    private string $clientId;

    private Consent $consent;

    public function __construct(string $query, string $clientId, Consent $consent)
    {
        $this->query = $query;
        $this->clientId = $clientId;
        $this->consent = $consent;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getConsent(): Consent
    {
        return $this->consent;
    }
}
