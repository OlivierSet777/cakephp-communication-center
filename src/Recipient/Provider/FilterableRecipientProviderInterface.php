<?php
declare(strict_types=1);

namespace CommunicationCenter\Recipient\Provider;

/**
 * Defines a recipient provider exposing filtering options.
 */
interface FilterableRecipientProviderInterface extends RecipientProviderInterface
{
    /**
     * Returns filters available for this provider.
     *
     * Each filter contains a label and a list of available options.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getFilters(): array;
}
