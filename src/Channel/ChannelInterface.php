<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel;

use CommunicationCenter\Recipient\Recipient;

/**
 * Defines a communication channel.
 */
interface ChannelInterface
{
    /**
     * Returns the unique channel name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Determines whether the recipient can use this channel.
     *
     * @param \CommunicationCenter\Recipient\Recipient $recipient Recipient.
     * @return bool
     */
    public function supports(Recipient $recipient): bool;

    /**
     * Prepares the channel action for a recipient.
     *
     * @param \CommunicationCenter\Recipient\Recipient $recipient Recipient.
     * @param string $message Rendered message.
     * @return \CommunicationCenter\Channel\ChannelAction
     */
    public function prepare(Recipient $recipient, string $message): ChannelAction;
}
