<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Entity;

use DateTimeInterface;

interface HitInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_SENT = 'sent';

    public const STATE_FAILED = 'failed';

    public function getId(): string;

    public function getClientId(): ?string;

    public function setClientId(?string $clientId): void;

    public function isConsentGranted(): bool;

    public function setConsentGranted(bool $consentGranted): void;

    public function getQuery(): ?string;

    public function setQuery(string $query): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): void;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;
}
