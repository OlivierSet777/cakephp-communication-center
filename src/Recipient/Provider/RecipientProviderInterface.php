<?php
declare(strict_types=1);

namespace CommunicationCenter\Recipient\Provider;

/**
 * Defines how a host application provides recipients
 * to Communication Center.
 */
interface RecipientProviderInterface
{
    /**
     * Returns the recipients available from this provider.
     *
     * @param array<string, mixed> $criteria Optional filtering criteria.
     * @return iterable<\CommunicationCenter\Recipient\Recipient>
     */
    public function getRecipients(array $criteria = []): iterable;
}
