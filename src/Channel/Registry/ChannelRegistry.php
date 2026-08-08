<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel\Registry;

use CommunicationCenter\Channel\ChannelInterface;
use InvalidArgumentException;

/**
 * Registry for communication channels.
 */
final class ChannelRegistry
{
    /**
     * Registered channels.
     *
     * @var array<string, \CommunicationCenter\Channel\ChannelInterface>
     */
    private array $channels = [];

    /**
     * Registers a communication channel.
     *
     * @param \CommunicationCenter\Channel\ChannelInterface $channel Channel.
     * @return void
     */
    public function set(ChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * Returns a registered communication channel.
     *
     * @param string $name Channel name.
     * @return \CommunicationCenter\Channel\ChannelInterface
     * @throws \InvalidArgumentException When the channel is not registered.
     */
    public function get(string $name): ChannelInterface
    {
        if (!isset($this->channels[$name])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Communication channel "%s" is not registered.',
                    $name,
                ),
            );
        }

        return $this->channels[$name];
    }

    /**
     * Checks whether a channel is registered.
     *
     * @param string $name Channel name.
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->channels[$name]);
    }

    /**
     * Returns all registered channels.
     *
     * @return array<string, \CommunicationCenter\Channel\ChannelInterface>
     */
    public function all(): array
    {
        return $this->channels;
    }
}
