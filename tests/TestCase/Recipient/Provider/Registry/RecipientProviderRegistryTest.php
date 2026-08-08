<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Recipient\Provider\Registry;

use CommunicationCenter\Recipient\Provider\RecipientProviderInterface;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Recipient\Recipient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RecipientProviderRegistryTest extends TestCase
{
    public function testCanRegisterProvider(): void
    {
        $registry = new RecipientProviderRegistry();

        $provider = $this->createProvider();

        $registry->set('members', $provider);

        $this->assertTrue($registry->has('members'));
    }

    public function testCanRetrieveRegisteredProvider(): void
    {
        $registry = new RecipientProviderRegistry();

        $provider = $this->createProvider();

        $registry->set('members', $provider);

        $this->assertSame(
            $provider,
            $registry->get('members'),
        );
    }

    public function testCanReturnAllRegisteredProviders(): void
    {
        $registry = new RecipientProviderRegistry();

        $provider = $this->createProvider();

        $registry->set('members', $provider);

        $providers = $registry->all();

        $this->assertCount(1, $providers);
        $this->assertSame(
            $provider,
            $providers['members'],
        );
    }

    public function testUnknownProviderThrowsException(): void
    {
        $registry = new RecipientProviderRegistry();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Recipient provider "unknown" is not registered.',
        );

        $registry->get('unknown');
    }

    private function createProvider(): RecipientProviderInterface
    {
        return new class implements RecipientProviderInterface {
            /**
             * @inheritDoc
             */
            public function getRecipients(array $criteria = []): iterable
            {
                yield new Recipient(
                    externalId: '145',
                    firstname: 'Jean',
                    phone: '+33 6 12 34 56 78',
                );
            }
        };
    }
}
