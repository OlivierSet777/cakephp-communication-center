<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Service;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CommunicationCenter\Email\EmailSenderInterface;
use CommunicationCenter\Model\Table\CommunicationCampaignsTable;
use CommunicationCenter\Service\CampaignService;
use CommunicationCenter\Service\EmailCampaignService;
use RuntimeException;

class EmailCampaignServiceTest extends TestCase
{
    private CommunicationCampaignsTable $campaigns;

    protected function setUp(): void
    {
        parent::setUp();

        $this->campaigns = TableRegistry::getTableLocator()->get(
            'CommunicationCenter.CommunicationCampaigns',
        );

        $this->campaigns->deleteAll([]);
    }

    public function testSendEmailAndMarkRecipientProcessed(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne email',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'lastname' => 'Dupont',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $sender
            ->expects($this->once())
            ->method('send')
            ->with(
                'jean@example.fr',
                'Informations importantes',
                'Bonjour Jean',
            );

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        $service->send(
            (int)$campaign->id,
            '1',
        );

        $saved = $campaignService->getCampaign(
            (int)$campaign->id,
        );

        $this->assertSame('completed', $saved->status);
        $this->assertSame(1, $saved->processed_count);

        $this->assertSame(
            'processed',
            $saved->communication_recipients[0]->status,
        );
    }

    public function testDoesNotSendWhatsAppCampaign(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne WhatsApp',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'phone' => '+33612345678',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
        );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $sender
            ->expects($this->never())
            ->method('send');

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'This campaign is not an email campaign.',
        );

        $service->send(
            (int)$campaign->id,
            '1',
        );
    }

    public function testDoesNotSendRecipientWithInvalidEmail(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne email',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $this->campaigns
            ->CommunicationRecipients
            ->updateAll(
                ['email' => 'email-invalide'],
                [
                    'communication_campaign_id' => $campaign->id,
                    'external_id' => '1',
                ],
            );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $sender
            ->expects($this->never())
            ->method('send');

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Recipient email address is invalid.',
        );

        $service->send(
            (int)$campaign->id,
            '1',
        );
    }

    public function testSenderFailureDoesNotMarkRecipientProcessed(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne email',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $sender
            ->expects($this->once())
            ->method('send')
            ->willThrowException(
                new RuntimeException(
                    'Unable to send email.',
                ),
            );

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        try {
            $service->send(
                (int)$campaign->id,
                '1',
            );

            $this->fail(
                'Expected email sender to throw an exception.',
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Unable to send email.',
                $exception->getMessage(),
            );
        }

        $saved = $campaignService->getCampaign(
            (int)$campaign->id,
        );

        $this->assertSame('ready', $saved->status);
        $this->assertSame(0, $saved->processed_count);

        $this->assertSame(
            'pending',
            $saved->communication_recipients[0]->status,
        );
    }

    public function testSendAllPendingRecipients(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne email multiple',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
                [
                    'external_id' => '2',
                    'firstname' => 'Marie',
                    'email' => 'marie@example.fr',
                    'rendered_message' => 'Bonjour Marie',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $sender
            ->expects($this->exactly(2))
            ->method('send');

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        $result = $service->sendAll(
            (int)$campaign->id,
        );

        $this->assertSame(
            [
                'sent' => 2,
                'failed' => 0,
            ],
            $result,
        );

        $saved = $campaignService->getCampaign(
            (int)$campaign->id,
        );

        $this->assertSame(2, $saved->processed_count);
        $this->assertSame('completed', $saved->status);

        $this->assertSame(
            'processed',
            $saved->communication_recipients[0]->status,
        );

        $this->assertSame(
            'processed',
            $saved->communication_recipients[1]->status,
        );
    }

    public function testSendAllContinuesWhenOneRecipientFails(): void
    {
        $campaignService = new CampaignService(
            $this->campaigns,
        );

        $campaign = $campaignService->create(
            name: 'Campagne email avec erreur',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
                [
                    'external_id' => '2',
                    'firstname' => 'Marie',
                    'email' => 'marie@example.fr',
                    'rendered_message' => 'Bonjour Marie',
                    'status' => 'pending',
                ],
                [
                    'external_id' => '3',
                    'firstname' => 'Paul',
                    'email' => 'paul@example.fr',
                    'rendered_message' => 'Bonjour Paul',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $sender = $this->createMock(
            EmailSenderInterface::class,
        );

        $call = 0;

        $sender
            ->expects($this->exactly(3))
            ->method('send')
            ->willReturnCallback(
                function () use (&$call): void {
                    $call++;

                    if ($call === 2) {
                        throw new RuntimeException(
                            'Unable to send email.',
                        );
                    }
                },
            );

        $service = new EmailCampaignService(
            $campaignService,
            $sender,
        );

        $result = $service->sendAll(
            (int)$campaign->id,
        );

        $this->assertSame(
            [
                'sent' => 2,
                'failed' => 1,
            ],
            $result,
        );

        $saved = $campaignService->getCampaign(
            (int)$campaign->id,
        );

        $this->assertSame(2, $saved->processed_count);
        $this->assertSame('processing', $saved->status);

        $statuses = [];

        foreach ($saved->communication_recipients as $recipient) {
            $statuses[$recipient->external_id] = $recipient->status;
        }

        $this->assertSame('processed', $statuses['1']);
        $this->assertSame('pending', $statuses['2']);
        $this->assertSame('processed', $statuses['3']);
    }
}
