<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Channel\WhatsApp;

use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use CommunicationCenter\Recipient\Recipient;
use PHPUnit\Framework\TestCase;

class WhatsAppChannelTest extends TestCase
{
    public function testChannelNameIsWhatsApp(): void
    {
        $channel = new WhatsAppChannel();

        $this->assertSame('whatsapp', $channel->getName());
    }

    public function testSupportsRecipientWithPhone(): void
    {
        $channel = new WhatsAppChannel();

        $recipient = new Recipient(
            externalId: '145',
            phone: '+33 6 12 34 56 78',
        );

        $this->assertTrue($channel->supports($recipient));
    }

    public function testDoesNotSupportRecipientWithoutPhone(): void
    {
        $channel = new WhatsAppChannel();

        $recipient = new Recipient(
            externalId: '145',
        );

        $this->assertFalse($channel->supports($recipient));
    }

    public function testPreparesFrenchWhatsAppUrl(): void
    {
        $channel = new WhatsAppChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            phone: '+33 6 12 34 56 78',
        );

        $action = $channel->prepare(
            $recipient,
            'Bonjour Jean !',
        );

        $this->assertSame('whatsapp', $action->channel);
        $this->assertSame('open_url', $action->action);
        $this->assertSame(
            'https://wa.me/33612345678?text=Bonjour%20Jean%20%21',
            $action->url,
        );
    }

    public function testSupportsDifferentCountries(): void
    {
        $channel = new WhatsAppChannel();

        $recipients = [
            new Recipient(
                externalId: 'fr',
                phone: '+33 6 12 34 56 78',
            ),
            new Recipient(
                externalId: 'au',
                phone: '+61 412 345 678',
            ),
            new Recipient(
                externalId: 'cn',
                phone: '+86 138 1234 5678',
            ),
        ];

        $expectedNumbers = [
            '33612345678',
            '61412345678',
            '8613812345678',
        ];

        foreach ($recipients as $index => $recipient) {
            $action = $channel->prepare(
                $recipient,
                'Hello',
            );

            $this->assertStringContainsString(
                'wa.me/' . $expectedNumbers[$index],
                $action->url ?? '',
            );
        }
    }

    public function testDoesNotSupportLocalPhoneNumber(): void
    {
        $channel = new WhatsAppChannel();

        $recipient = new Recipient(
            externalId: '145',
            phone: '06 12 34 56 78',
        );

        $this->assertFalse($channel->supports($recipient));
    }

    public function testDoesNotSupportInvalidPhoneNumber(): void
    {
        $channel = new WhatsAppChannel();

        $recipient = new Recipient(
            externalId: '145',
            phone: 'bonjour',
        );

        $this->assertFalse($channel->supports($recipient));
    }
}
