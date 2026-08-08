<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Service;

use CommunicationCenter\Channel\WhatsApp\WhatsAppChannel;
use CommunicationCenter\Message\SimpleMessageRenderer;
use CommunicationCenter\Recipient\Recipient;
use CommunicationCenter\Service\CommunicationService;
use PHPUnit\Framework\TestCase;

class CommunicationServiceTest extends TestCase
{
    public function testPreparesPersonalizedWhatsAppActions(): void
    {
        $recipients = [
            new Recipient(
                externalId: '145',
                firstname: 'Jean',
                phone: '+33 6 12 34 56 78',
                variables: [
                    'month' => 'Août',
                ],
            ),
            new Recipient(
                externalId: '287',
                firstname: 'Jack',
                phone: '+61 412 345 678',
                variables: [
                    'month' => 'August',
                ],
            ),
            new Recipient(
                externalId: '392',
                firstname: 'Li Wei',
                phone: '+86 138 1234 5678',
                variables: [
                    'month' => '八月',
                ],
            ),
        ];

        $service = new CommunicationService(
            new SimpleMessageRenderer(),
        );

        $actions = $service->prepare(
            $recipients,
            'Bonjour {{firstname}}, cotisation {{month}}.',
            new WhatsAppChannel(),
        );

        $this->assertCount(3, $actions);

        $this->assertStringContainsString(
            'wa.me/33612345678',
            $actions[0]->url ?? '',
        );

        $this->assertStringContainsString(
            rawurlencode('Bonjour Jean, cotisation Août.'),
            $actions[0]->url ?? '',
        );

        $this->assertStringContainsString(
            'wa.me/61412345678',
            $actions[1]->url ?? '',
        );

        $this->assertStringContainsString(
            rawurlencode('Bonjour Jack, cotisation August.'),
            $actions[1]->url ?? '',
        );

        $this->assertStringContainsString(
            'wa.me/8613812345678',
            $actions[2]->url ?? '',
        );

        $this->assertStringContainsString(
            rawurlencode('Bonjour Li Wei, cotisation 八月.'),
            $actions[2]->url ?? '',
        );
    }

    public function testIgnoresUnsupportedRecipients(): void
    {
        $recipients = [
            new Recipient(
                externalId: '145',
                firstname: 'Jean',
                phone: '+33 6 12 34 56 78',
            ),
            new Recipient(
                externalId: '146',
                firstname: 'Paul',
                phone: '06 12 34 56 78',
            ),
            new Recipient(
                externalId: '147',
                firstname: 'Marie',
            ),
        ];

        $service = new CommunicationService(
            new SimpleMessageRenderer(),
        );

        $actions = $service->prepare(
            $recipients,
            'Bonjour {{firstname}}.',
            new WhatsAppChannel(),
        );

        $this->assertCount(1, $actions);
        $this->assertStringContainsString(
            'wa.me/33612345678',
            $actions[0]->url ?? '',
        );
    }
}
