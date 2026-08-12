<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Channel\Email;

use Cake\TestSuite\TestCase;
use CommunicationCenter\Channel\Email\EmailChannel;
use CommunicationCenter\Recipient\Recipient;

class EmailChannelTest extends TestCase
{
    public function testGetName(): void
    {
        $channel = new EmailChannel();

        $this->assertSame(
            'email',
            $channel->getName(),
        );
    }

    public function testSupportsRecipientWithValidEmail(): void
    {
        $channel = new EmailChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: null,
            email: 'jean@example.fr',
        );

        $this->assertTrue(
            $channel->supports($recipient),
        );
    }

    public function testDoesNotSupportRecipientWithoutEmail(): void
    {
        $channel = new EmailChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: null,
            email: null,
        );

        $this->assertFalse(
            $channel->supports($recipient),
        );
    }

    public function testDoesNotSupportRecipientWithInvalidEmail(): void
    {
        $channel = new EmailChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: null,
            email: 'email-invalide',
        );

        $this->assertFalse(
            $channel->supports($recipient),
        );
    }

    public function testPrepare(): void
    {
        $channel = new EmailChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: null,
            email: 'jean@example.fr',
        );

        $action = $channel->prepare(
            $recipient,
            'Bonjour Jean',
            [
                'subject' => 'Informations importantes',
            ],
        );

        $this->assertSame('email', $action->channel);
        $this->assertSame('open_url', $action->action);
        $this->assertSame('145', $action->recipientId);
        $this->assertSame('Bonjour Jean', $action->message);

        $this->assertSame(
            'mailto:jean%40example.fr'
                . '?subject=Informations%20importantes'
                . '&body=Bonjour%20Jean',
            $action->url,
        );

        $this->assertSame(
            'Informations importantes',
            $action->metadata['subject'],
        );
    }

    public function testPrepareWithoutSubject(): void
    {
        $channel = new EmailChannel();

        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: null,
            email: 'jean@example.fr',
        );

        $action = $channel->prepare(
            $recipient,
            'Bonjour Jean',
        );

        $this->assertSame(
            'mailto:jean%40example.fr?subject=&body=Bonjour%20Jean',
            $action->url,
        );

        $this->assertSame(
            '',
            $action->metadata['subject'],
        );
    }
}
