<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel\Email;

use CommunicationCenter\Channel\ChannelAction;
use CommunicationCenter\Channel\ChannelInterface;
use CommunicationCenter\Recipient\Recipient;

/**
 * Prepares email actions for recipients.
 */
final class EmailChannel implements ChannelInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'email';
    }

    /**
     * @inheritDoc
     */
    public function supports(Recipient $recipient): bool
    {
        if ($recipient->email === null) {
            return false;
        }

        return filter_var(
            $recipient->email,
            FILTER_VALIDATE_EMAIL,
        ) !== false;
    }

    /**
     * Undocumented function
     *
     * @param \CommunicationCenter\Recipient\Recipient $recipient
     * @param string $message
     * @param array $options
     * @return \CommunicationCenter\Channel\ChannelAction
     */
    public function prepare(
        Recipient $recipient,
        string $message,
        array $options = [],
    ): ChannelAction {
        $subject = $options['subject'] ?? null;

        if (!is_string($subject)) {
            $subject = '';
        }

        return new ChannelAction(
            channel: $this->getName(),
            action: 'open_url',
            url: sprintf(
                'mailto:%s?subject=%s&body=%s',
                rawurlencode($recipient->email ?? ''),
                rawurlencode($subject),
                rawurlencode($message),
            ),
            recipientId: $recipient->externalId,
            message: $message,
            metadata: [
                'subject' => $subject,
            ],
        );
    }
}
