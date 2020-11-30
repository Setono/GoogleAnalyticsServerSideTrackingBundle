<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Entity;

use DateTimeInterface;
use Setono\GoogleAnalyticsMeasurementProtocol\Event\EventInterface as MeasurementProtocolEventInterface;

interface EventInterface
{
    public function getId(): string;

    public function getClientId(): ?string;

    public function setClientId(string $clientId): void;

    public function getUserId(): ?string;

    public function setUserId(?string $userId): void;

    public function hasConsent(): bool;

    public function setConsent(bool $consent): void;

    public function getEvent(): ?MeasurementProtocolEventInterface;

    public function setEvent(MeasurementProtocolEventInterface $event): void;

    public function getCreatedAt(): DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): void;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;
}
