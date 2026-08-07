<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Message;

use CommunicationCenter\Message\SimpleMessageRenderer;
use CommunicationCenter\Recipient\Recipient;
use PHPUnit\Framework\TestCase;

class SimpleMessageRendererTest extends TestCase
{
    public function testRendersStandardRecipientVariables(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            lastname: 'Dupont',
            phone: '0768596963',
            email: 'jean@example.fr',
        );

        $renderer = new SimpleMessageRenderer();

        $message = $renderer->render(
            'Bonjour {{firstname}} {{lastname}}.',
            $recipient,
        );

        $this->assertSame('Bonjour Jean Dupont.', $message);
    }

    public function testRendersCustomVariables(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            variables: [
                'recyclery' => 'Cahors',
                'month' => 'Août',
            ],
        );

        $renderer = new SimpleMessageRenderer();

        $message = $renderer->render(
            'Bonjour {{firstname}}, votre cotisation de {{month}} pour {{recyclery}} est en attente.',
            $recipient,
        );

        $this->assertSame(
            'Bonjour Jean, votre cotisation de Août pour Cahors est en attente.',
            $message,
        );
    }

    public function testMissingStandardValueIsReplacedByEmptyString(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
        );

        $renderer = new SimpleMessageRenderer();

        $message = $renderer->render(
            'Bonjour {{firstname}} {{lastname}}.',
            $recipient,
        );

        $this->assertSame('Bonjour Jean .', $message);
    }

    public function testStandardVariablesCannotBeOverridden(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            firstname: 'Jean',
            variables: [
                'firstname' => 'Paul',
            ],
        );

        $renderer = new SimpleMessageRenderer();

        $message = $renderer->render(
            'Bonjour {{firstname}}.',
            $recipient,
        );

        $this->assertSame('Bonjour Jean.', $message);
    }

    public function testNonScalarVariablesAreIgnored(): void
    {
        $recipient = new Recipient(
            externalId: '145',
            variables: [
                'categories' => ['one', 'two'],
            ],
        );

        $renderer = new SimpleMessageRenderer();

        $message = $renderer->render(
            'Catégories : {{categories}}',
            $recipient,
        );

        $this->assertSame(
            'Catégories : {{categories}}',
            $message,
        );
    }
}
