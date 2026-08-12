<?php
declare(strict_types=1);

namespace CommunicationCenter\Service;

use CommunicationCenter\Channel\ChannelInterface;
use CommunicationCenter\Message\MessageRendererInterface;
use CommunicationCenter\Recipient\Recipient;

/**
 * Orchestrates message rendering and channel action preparation.
 */
final readonly class CommunicationService
{
    /**
     * Constructor.
     *
     * @param \CommunicationCenter\Message\MessageRendererInterface $renderer Message renderer.
     */
    public function __construct(
        private MessageRendererInterface $renderer,
    ) {
    }

    /**
     * Prepares communication actions for recipients.
     *
     * Unsupported recipients are ignored.
     *
     * @param iterable<\CommunicationCenter\Recipient\Recipient> $recipients Recipients.
     * @param string $template Message template.
     * @param \CommunicationCenter\Channel\ChannelInterface $channel Communication channel.
     * @param array<string, mixed> $options Channel options.
     * @return array<\CommunicationCenter\Channel\ChannelAction>
     */
    public function prepare(
        iterable $recipients,
        string $template,
        ChannelInterface $channel,
        array $options = [],
    ): array {
        $actions = [];

        foreach ($recipients as $recipient) {
            if (!$recipient instanceof Recipient) {
                continue;
            }

            if (!$channel->supports($recipient)) {
                continue;
            }

            $message = $this->renderer->render(
                $template,
                $recipient,
            );

            $actions[] = $channel->prepare(
                $recipient,
                $message,
                $options,
            );
        }

        return $actions;
    }
}
