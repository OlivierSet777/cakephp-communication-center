<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel;

/**
 * Represents an action prepared by a communication channel.
 */
final readonly class ChannelAction
{
    /**
     * Constructor.
     *
     * @param string $channel Channel identifier.
     * @param string $action Action type.
     * @param string|null $url URL to open, when applicable.
     * @param array<string, mixed> $metadata Additional channel-specific data.
     */
    public function __construct(
        public string $channel,
        public string $action,
        public ?string $url = null,
        public array $metadata = [],
    ) {
    }
}
