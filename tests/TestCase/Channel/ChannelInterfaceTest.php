<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Channel;

use CommunicationCenter\Channel\ChannelAction;
use CommunicationCenter\Channel\ChannelInterface;
use CommunicationCenter\Recipient\Recipient;
use PHPUnit\Framework\TestCase;

class ChannelInterfaceTest extends TestCase
{
    public function testChannelCanPrepareAction(): void
    {
        $channel = new class implements ChannelInterface {
            public function getName(): string
            {
                return 'fake';
            }

            public function supports(Recipient $recipient): bool
            {
                return $recipient->phone !== null;
            }

            public function prepare(Recipient $recipient, string $message, array $options = []): ChannelAction
            {
                return new ChannelAction(
                    channel: $this->getName(),
                    action: 'open_url',
                    url: 'https://example.test',
                    metadata: [
                        'message' => $message,
                    ],
                );
            }
        };

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            phone: '0768596963',
        );

        $this->assertSame('fake', $channel->getName());
        $this->assertTrue($channel->supports($recipient));

        $action = $channel->prepare(
            $recipient,
            'Bonjour Jean',
        );

        $this->assertSame('fake', $action->channel);
        $this->assertSame('open_url', $action->action);
        $this->assertSame('https://example.test', $action->url);
        $this->assertSame('Bonjour Jean', $action->metadata['message']);
    }

    public function testChannelCanRejectUnsupportedRecipient(): void
    {
        $channel = new class implements ChannelInterface {
            public function getName(): string
            {
                return 'fake';
            }

            public function supports(Recipient $recipient): bool
            {
                return $recipient->phone !== null;
            }

            public function prepare(Recipient $recipient, string $message, array $options = []): ChannelAction
            {
                return new ChannelAction(
                    channel: $this->getName(),
                    action: 'open_url',
                );
            }
        };

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
        );

        $this->assertFalse($channel->supports($recipient));
    }
}
