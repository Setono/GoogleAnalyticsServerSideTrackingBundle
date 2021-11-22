<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Entity;

use DateTime;
use DateTimeInterface;
use Setono\ClientId\ClientId;
use Setono\GoogleAnalyticsMeasurementProtocol\Hit\HitBuilderInterface;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Message\Command\PersistHit;
use Symfony\Component\Uid\Uuid;

class Hit implements HitInterface
{
    protected string $id;

    protected ?ClientId $clientId = null;

    protected bool $consentGranted = false;

    protected ?string $query = null;

    protected string $state = self::STATE_PENDING;

    protected ?string $bulkIdentifier = null;

    protected DateTimeInterface $createdAt;

    protected ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
        $this->createdAt = new DateTime();
    }

    public static function createFromHitBuilder(HitBuilderInterface $hitBuilder, string $property): self
    {
        $obj = new self();
        $obj->setClientId(new ClientId((string) $hitBuilder->getClientId()));
        $obj->setQuery($hitBuilder->getHit($property)->getPayload());

        return $obj;
    }

    public static function createFromCommand(PersistHit $command): self
    {
        $obj = new self();
        $obj->setQuery($command->getQuery());
        $obj->setClientId(new ClientId($command->getClientId()));
        $obj->setConsentGranted($command->getConsent()->isMarketingConsentGranted());

        return $obj;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): ?ClientId
    {
        return $this->clientId;
    }

    public function setClientId(?ClientId $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function isConsentGranted(): bool
    {
        return $this->consentGranted;
    }

    public function setConsentGranted(bool $consentGranted): void
    {
        $this->consentGranted = $consentGranted;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): void
    {
        $this->query = $query;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
