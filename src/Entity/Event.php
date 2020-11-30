<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Entity;

use DateTimeInterface;
use Safe\DateTime;
use Setono\GoogleAnalyticsMeasurementProtocol\Event\EventInterface as MeasurementProtocolEventInterface;
use Symfony\Component\Uid\Uuid;

class Event implements EventInterface
{
    protected string $id;

    protected ?string $clientId = null;

    protected ?string $userId = null;

    protected bool $consent = false;

    protected ?MeasurementProtocolEventInterface $event = null;

    protected DateTimeInterface $createdAt;

    protected ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function hasConsent(): bool
    {
        return $this->consent;
    }

    public function setConsent(bool $consent): void
    {
        $this->consent = $consent;
    }

    public function getEvent(): ?MeasurementProtocolEventInterface
    {
        return $this->event;
    }

    public function setEvent(MeasurementProtocolEventInterface $event): void
    {
        $this->event = $event;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
