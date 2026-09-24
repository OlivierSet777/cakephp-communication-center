<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel\Sms;

use CommunicationCenter\Channel\ChannelAction;
use CommunicationCenter\Channel\ChannelInterface;
use CommunicationCenter\Phone\PhoneNumber;
use CommunicationCenter\Recipient\Recipient;

/**
 * Prepares SMS actions for recipients.
 */
final class SmsChannel implements ChannelInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'sms';
    }

    /**
     * @inheritDoc
     */
    public function supports(Recipient $recipient): bool
    {
        if ($recipient->phone === null) {
            return false;
        }

        $phone = new PhoneNumber($recipient->phone);

        return $phone->isValid();
    }

    /**
     * @inheritDoc
     */
    public function prepare(
        Recipient $recipient,
        string $message,
        array $options = [],
    ): ChannelAction {
        $phone = new PhoneNumber($recipient->phone ?? '');

        return new ChannelAction(
            channel: $this->getName(),
            action: 'open_url',
            url: sprintf(
                'sms:%s?body=%s',
                $phone->normalized(),
                rawurlencode($message),
            ),
            recipientId: $recipient->externalId,
            message: $message,
        );
    }
}
