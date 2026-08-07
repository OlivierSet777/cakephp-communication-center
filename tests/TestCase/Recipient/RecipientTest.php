<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Recipient;

use CommunicationCenter\Recipient\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    public function testCanBeCreatedWithOnlyExternalId(): void
    {
        $recipient = new Recipient('145');

        $this->assertSame('145', $recipient->externalId);
        $this->assertNull($recipient->firstname);
        $this->assertNull($recipient->lastname);
        $this->assertNull($recipient->phone);
        $this->assertNull($recipient->email);
        $this->assertSame([], $recipient->variables);
    }

    public function testCanContainContactInformation(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: '0768596963',
            email: 'jean@example.fr',
        );

        $this->assertSame('Jean', $recipient->firstname);
        $this->assertSame('Dupont', $recipient->lastname);
        $this->assertSame('0768596963', $recipient->phone);
        $this->assertSame('jean@example.fr', $recipient->email);
    }

    public function testCanContainCustomVariables(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            variables: [
                'recyclery' => 'Cahors',
                'day' => 'Mercredi',
                'month' => 'Août',
            ],
        );

        $this->assertSame('Cahors', $recipient->variables['recyclery']);
        $this->assertSame('Mercredi', $recipient->variables['day']);
        $this->assertSame('Août', $recipient->variables['month']);
    }
}
