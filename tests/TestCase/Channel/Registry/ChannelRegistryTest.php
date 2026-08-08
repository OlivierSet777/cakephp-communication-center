<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Channel\Registry;

use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChannelRegistryTest extends TestCase
{
    public function testCanRegisterChannel(): void
    {
        $registry = new ChannelRegistry();

        $registry->set(new WhatsAppChannel());

        $this->assertTrue($registry->has('whatsapp'));
    }

    public function testCanRetrieveRegisteredChannel(): void
    {
        $registry = new ChannelRegistry();

        $channel = new WhatsAppChannel();

        $registry->set($channel);

        $this->assertSame(
            $channel,
            $registry->get('whatsapp'),
        );
    }

    public function testCanReturnAllRegisteredChannels(): void
    {
        $registry = new ChannelRegistry();

        $channel = new WhatsAppChannel();

        $registry->set($channel);

        $channels = $registry->all();

        $this->assertCount(1, $channels);
        $this->assertSame(
            $channel,
            $channels['whatsapp'],
        );
    }

    public function testUnknownChannelThrowsException(): void
    {
        $registry = new ChannelRegistry();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Communication channel "sms" is not registered.',
        );

        $registry->get('sms');
    }
}
