<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Entity;

use DateTimeInterface;
use Setono\ClientId\ClientId;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\Hit as BaseHit;

interface HitInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_SENT = 'sent';

    public const STATE_FAILED = 'failed';

    public function getId(): string;

    public function getClientId(): ?ClientId;

    public function setClientId(?ClientId $clientId): void;

    public function isConsentGranted(): bool;

    public function setConsentGranted(bool $consentGranted): void;

    public function getHit(): ?BaseHit;

    public function setHit(?BaseHit $hit): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): void;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;
}
