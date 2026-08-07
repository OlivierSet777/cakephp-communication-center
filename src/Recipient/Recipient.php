<?php
declare(strict_types=1);

namespace CommunicationCenter\Recipient;

/**
 * Represents a normalized communication recipient.
 *
 * A recipient is independent from the host application's business model.
 */
final readonly class Recipient
{
    /**
     * Constructor.
     *
     * @param string $externalId Identifier from the source application.
     * @param string|null $firstname Recipient first name.
     * @param string|null $lastname Recipient last name.
     * @param string|null $phone Recipient phone number.
     * @param string|null $email Recipient email address.
     * @param array<string, mixed> $variables Additional template variables.
     */
    public function __construct(
        public string $externalId,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $phone = null,
        public ?string $email = null,
        public array $variables = [],
    ) {
    }
}
