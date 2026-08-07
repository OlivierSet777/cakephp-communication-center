<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Recipient\Provider;

use CommunicationCenter\Recipient\Provider\RecipientProviderInterface;
use CommunicationCenter\Recipient\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientProviderInterfaceTest extends TestCase
{
    public function testProviderCanReturnRecipients(): void
    {
        $provider = new class implements RecipientProviderInterface {
            /**
             * @inheritDoc
             */
            public function getRecipients(array $criteria = []): iterable
            {
                yield new Recipient(
                    externalId: '145',
                    firstname: 'Jean',
                    lastname: 'Dupont',
                    phone: '0768596963',
                    email: 'jean@example.fr',
                    variables: [
                        'recyclery' => 'Cahors',
                    ],
                );
            }
        };

        $recipients = iterator_to_array($provider->getRecipients());

        $this->assertCount(1, $recipients);
        $this->assertInstanceOf(Recipient::class, $recipients[0]);
        $this->assertSame('145', $recipients[0]->externalId);
        $this->assertSame('Jean', $recipients[0]->firstname);
        $this->assertSame('Cahors', $recipients[0]->variables['recyclery']);
    }

    public function testProviderCanReceiveCriteria(): void
    {
        $provider = new class implements RecipientProviderInterface {
            /**
             * @inheritDoc
             */
            public function getRecipients(array $criteria = []): iterable
            {
                if (($criteria['status'] ?? null) !== 'unpaid') {
                    return;
                }

                yield new Recipient(
                    externalId: '145',
                    firstname: 'Jean',
                    phone: '0768596963',
                );
            }
        };

        $recipients = iterator_to_array(
            $provider->getRecipients([
                'status' => 'unpaid',
            ]),
        );

        $this->assertCount(1, $recipients);
        $this->assertSame('145', $recipients[0]->externalId);
    }
}
