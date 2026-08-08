<?php
declare(strict_types=1);

namespace CommunicationCenter\Channel\WhatsApp;

use CommunicationCenter\Channel\ChannelAction;
use CommunicationCenter\Channel\ChannelInterface;
use CommunicationCenter\Recipient\Recipient;

/**
 * Prepares WhatsApp actions for recipients.
 */
final class WhatsAppChannel implements ChannelInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'whatsapp';
    }

    /**
     * @inheritDoc
     */
    public function supports(Recipient $recipient): bool
    {
        return $recipient->phone !== null
            && trim($recipient->phone) !== '';
    }

    /**
     * @inheritDoc
     */
    public function prepare(
        Recipient $recipient,
        string $message,
    ): ChannelAction {
        $phone = $this->normalizePhone(
            $recipient->phone ?? '',
        );

        return new ChannelAction(
            channel: $this->getName(),
            action: 'open_url',
            url: sprintf(
                'https://wa.me/%s?text=%s',
                $phone,
                rawurlencode($message),
            ),
        );
    }

    /**
     * Converts an international phone number
     * into the format expected by WhatsApp.
     *
     * @param string $phone International phone number.
     * @return string
     */
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
