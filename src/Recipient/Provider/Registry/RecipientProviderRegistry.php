<?php
declare(strict_types=1);

namespace CommunicationCenter\Recipient\Provider\Registry;

use CommunicationCenter\Recipient\Provider\RecipientProviderInterface;
use InvalidArgumentException;

/**
 * Registry for recipient providers.
 */
final class RecipientProviderRegistry
{
    /**
     * Registered providers.
     *
     * @var array<string, \CommunicationCenter\Recipient\Provider\RecipientProviderInterface>
     */
    private array $providers = [];

    /**
     * Registers a recipient provider.
     *
     * @param string $name Provider name.
     * @param \CommunicationCenter\Recipient\Provider\RecipientProviderInterface $provider Provider.
     * @return void
     */
    public function set(
        string $name,
        RecipientProviderInterface $provider,
    ): void {
        $this->providers[$name] = $provider;
    }

    /**
     * Returns a registered recipient provider.
     *
     * @param string $name Provider name.
     * @return \CommunicationCenter\Recipient\Provider\RecipientProviderInterface
     * @throws \InvalidArgumentException When the provider is not registered.
     */
    public function get(string $name): RecipientProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Recipient provider "%s" is not registered.',
                    $name,
                ),
            );
        }

        return $this->providers[$name];
    }

    /**
     * Checks whether a provider is registered.
     *
     * @param string $name Provider name.
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->providers[$name]);
    }

    /**
     * Returns all registered providers.
     *
     * @return array<string, \CommunicationCenter\Recipient\Provider\RecipientProviderInterface>
     */
    public function all(): array
    {
        return $this->providers;
    }
}
