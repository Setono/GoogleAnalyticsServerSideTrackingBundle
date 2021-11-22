<?php

declare(strict_types=1);

namespace Setono\GoogleAnalyticsServerSideTrackingBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ObjectRepository;
use Setono\ClientId\ClientId;
use Setono\Consent\Consent;
use Setono\GoogleAnalyticsServerSideTrackingBundle\Entity\HitInterface;

interface HitRepositoryInterface extends ObjectRepository, ServiceEntityRepositoryInterface
{
    /**
     * Returns true if there are pending consented hits created before $delay seconds ago
     *
     * @param int $delay in seconds
     */
    public function hasConsentedPending(int $delay = 0): bool;

    /**
     * Will assign the given bulk identifier to pending consented hits
     *
     * @param int $delay in seconds
     * @param int $limit maximum number of rows to update
     */
    public function assignBulkIdentifierToPendingConsented(string $bulkIdentifier, int $delay = 0, int $limit = 1000): void;

    /**
     * @return array<array-key, HitInterface>
     */
    public function findByBulkIdentifier(string $bulkIdentifier): array;

    /**
     * This method will update the consent on the given client id
     */
    public function updateConsentOnClientId(ClientId $clientId, Consent $consent): void;
}
