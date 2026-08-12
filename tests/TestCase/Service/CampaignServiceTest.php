<?php
declare(strict_types=1);

namespace CommunicationCenter\Test\TestCase\Service;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use CommunicationCenter\Model\Table\CommunicationCampaignsTable;
use CommunicationCenter\Service\CampaignService;
use RuntimeException;

class CampaignServiceTest extends TestCase
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

    public function testGetDashboardStats(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $first = $service->create(
            name: 'Campagne en cours',
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
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $archived = $service->create(
            name: 'Campagne archivée',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '3',
                    'firstname' => 'Paul',
                    'phone' => '+33698765432',
                    'rendered_message' => 'Bonjour Paul',
                    'status' => 'pending',
                ],
            ],
        );

        // La première campagne passe en processing.
        $service->markRecipientProcessed(
            (int)$first->id,
            '1',
        );

        // La seconde est archivée.
        $service->archiveCampaign(
            (int)$archived->id,
        );

        $stats = $service->getDashboardStats();

        $this->assertSame(1, $stats['campaigns']);
        $this->assertSame(1, $stats['processing']);
        $this->assertSame(0, $stats['completed']);
        $this->assertSame(1, $stats['archived']);
    }

    public function testRestoreCampaign(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Campagne archivée',
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

        $service->archiveCampaign(
            (int)$campaign->id,
        );

        $archivedCampaigns = $service
            ->getArchivedCampaignsQuery()
            ->all();

        $this->assertCount(1, $archivedCampaigns);

        $restoredCampaign = $service->restoreCampaign(
            (int)$campaign->id,
        );

        $this->assertFalse(
            $restoredCampaign->archived,
        );

        $campaigns = $service->getCampaigns();

        $this->assertCount(1, $campaigns);

        $archivedCampaigns = $service
            ->getArchivedCampaignsQuery()
            ->all();

        $this->assertCount(0, $archivedCampaigns);
    }

    public function testArchiveCampaign(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Campagne à archiver',
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

        $this->assertFalse($campaign->archived);

        $archivedCampaign = $service->archiveCampaign(
            (int)$campaign->id,
        );

        $this->assertTrue($archivedCampaign->archived);

        $savedCampaign = $this->campaigns->get(
            $campaign->id,
        );

        $this->assertTrue($savedCampaign->archived);

        $campaigns = $service->getCampaigns();
        $this->assertCount(0, $campaigns);
    }

    public function testGetCampaignWithRecipients(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $created = $service->create(
            name: 'Campagne à reprendre',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'lastname' => 'Dupont',
                    'phone' => '+33612345678',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'lastname' => 'Smith',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $campaign = $service->getCampaign(
            (int)$created->id,
        );

        $this->assertSame(
            $created->id,
            $campaign->id,
        );

        $this->assertSame(
            'Campagne à reprendre',
            $campaign->name,
        );

        $this->assertSame(
            'ready',
            $campaign->status,
        );

        $this->assertCount(
            2,
            $campaign->communication_recipients,
        );
    }

    public function testCreatesCampaignWithRecipients(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Relance cotisations août',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '145',
                    'firstname' => 'Jean',
                    'lastname' => 'Dupont',
                    'phone' => '+33612345678',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
                [
                    'external_id' => '287',
                    'firstname' => 'Jack',
                    'lastname' => 'Smith',
                    'phone' => '+61412345678',
                    'email' => 'jack@example.com',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $this->assertNotNull($campaign->id);
        $this->assertSame(
            'Relance cotisations août',
            $campaign->name,
        );
        $this->assertSame('demo', $campaign->provider);
        $this->assertSame('whatsapp', $campaign->channel);
        $this->assertSame('ready', $campaign->status);
        $this->assertSame(2, $campaign->recipients_count);
        $this->assertSame(0, $campaign->processed_count);

        $savedCampaign = $this->campaigns->get(
            $campaign->id,
            contain: ['CommunicationRecipients'],
        );

        $this->assertCount(
            2,
            $savedCampaign->communication_recipients,
        );

        $this->assertSame(
            '145',
            $savedCampaign->communication_recipients[0]->external_id,
        );

        $this->assertSame(
            'Bonjour Jean',
            $savedCampaign->communication_recipients[0]->rendered_message,
        );
    }

    public function testCampaignContainsRecipientSnapshot(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Test snapshot',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '145',
                    'firstname' => 'Jean',
                    'lastname' => 'Dupont',
                    'phone' => '+33612345678',
                    'email' => 'jean@example.fr',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
        );

        $savedCampaign = $this->campaigns->get(
            $campaign->id,
            contain: ['CommunicationRecipients'],
        );

        $recipient = $savedCampaign->communication_recipients[0];

        $this->assertSame('Jean', $recipient->firstname);
        $this->assertSame('Dupont', $recipient->lastname);
        $this->assertSame('+33612345678', $recipient->phone);
        $this->assertSame('jean@example.fr', $recipient->email);
    }

    public function testCreateCampaignWithSubject(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Campagne email',
            provider: 'demo',
            channel: 'email',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '1',
                    'firstname' => 'Jean',
                    'lastname' => 'Dupont',
                    'phone' => '+33612345678',
                    'email' => 'jean.dupont@example.com',
                    'rendered_message' => 'Bonjour Jean',
                    'status' => 'pending',
                ],
            ],
            subject: 'Informations importantes',
        );

        $saved = $this->campaigns->get(
            $campaign->id,
        );

        $this->assertSame(
            'Informations importantes',
            $saved->subject,
        );

        $this->assertSame(
            'email',
            $saved->channel,
        );
    }

    public function testDoesNotPersistInvalidCampaign(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to create communication campaign.',
        );

        try {
            $service->create(
                name: '',
                provider: 'demo',
                channel: 'whatsapp',
                messageTemplate: 'Bonjour {{firstname}}',
                recipients: [
                    [
                        'external_id' => '145',
                        'firstname' => 'Jean',
                        'phone' => '+33612345678',
                        'rendered_message' => 'Bonjour Jean',
                        'status' => 'pending',
                    ],
                ],
            );
        } finally {
            $this->assertSame(
                0,
                $this->campaigns->find()->count(),
            );
        }
    }

    public function testGetCampaignsFiltersByChannel(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $service->create(
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

        $service->create(
            name: 'Campagne SMS',
            provider: 'demo',
            channel: 'sms',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $campaigns = $service->getCampaigns(
            channel: 'whatsapp',
        );

        $this->assertCount(1, $campaigns);
        $this->assertSame(
            'Campagne WhatsApp',
            $campaigns[0]->name,
        );
    }

    public function testGetCampaignsFiltersByStatus(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $service->create(
            name: 'Campagne prête',
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

        $completed = $service->create(
            name: 'Campagne terminée',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $this->campaigns->updateAll(
            ['status' => 'completed'],
            ['id' => $completed->id],
        );

        $campaigns = $service->getCampaigns(
            status: 'completed',
        );

        $this->assertCount(1, $campaigns);
        $this->assertSame(
            'Campagne terminée',
            $campaigns[0]->name,
        );
    }

    public function testGetCampaignsRespectsLimit(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        for ($i = 1; $i <= 3; $i++) {
            $service->create(
                name: 'Campagne ' . $i,
                provider: 'demo',
                channel: 'whatsapp',
                messageTemplate: 'Bonjour {{firstname}}',
                recipients: [
                    [
                        'external_id' => (string)$i,
                        'firstname' => 'Utilisateur ' . $i,
                        'phone' => '+3361234567' . $i,
                        'rendered_message' => 'Bonjour',
                        'status' => 'pending',
                    ],
                ],
            );
        }

        $campaigns = $service->getCampaigns(2);

        $this->assertCount(2, $campaigns);
    }

    public function testGetCampaignsReturnsMostRecentCampaigns(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $first = $service->create(
            name: 'Première campagne',
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

        $second = $service->create(
            name: 'Deuxième campagne',
            provider: 'demo',
            channel: 'whatsapp',
            messageTemplate: 'Bonjour {{firstname}}',
            recipients: [
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        /*
        * Force different creation dates so the test does not depend
        * on execution speed.
        */
        $this->campaigns->updateAll(
            ['created' => '2026-08-08 10:00:00'],
            ['id' => $first->id],
        );

        $this->campaigns->updateAll(
            ['created' => '2026-08-08 11:00:00'],
            ['id' => $second->id],
        );

        $campaigns = $service->getCampaigns();

        $this->assertCount(2, $campaigns);

        $this->assertSame(
            'Deuxième campagne',
            $campaigns[0]->name,
        );

        $this->assertSame(
            'Première campagne',
            $campaigns[1]->name,
        );
    }

    public function testMarkRecipientProcessed(): void
    {
        $service = new CampaignService(
            $this->campaigns,
        );

        $campaign = $service->create(
            name: 'Test processing',
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
                [
                    'external_id' => '2',
                    'firstname' => 'Jack',
                    'phone' => '+61412345678',
                    'rendered_message' => 'Bonjour Jack',
                    'status' => 'pending',
                ],
            ],
        );

        $campaign = $service->markRecipientProcessed(
            (int)$campaign->id,
            '1',
        );

        $this->assertSame('processing', $campaign->status);
        $this->assertSame(1, $campaign->processed_count);

        $campaign = $this->campaigns->get(
            $campaign->id,
            contain: ['CommunicationRecipients'],
        );

        $processedRecipient = null;

        foreach ($campaign->communication_recipients as $recipient) {
            if ($recipient->external_id === '1') {
                $processedRecipient = $recipient;
                break;
            }
        }

        $this->assertNotNull($processedRecipient);

        $this->assertSame(
            'processed',
            $processedRecipient->status,
        );

        $this->assertNotNull(
            $processedRecipient->processed,
        );

        // Processing the same recipient twice must be idempotent.
        $campaign = $service->markRecipientProcessed(
            (int)$campaign->id,
            '1',
        );

        $this->assertSame(1, $campaign->processed_count);

        $campaign = $service->markRecipientProcessed(
            (int)$campaign->id,
            '2',
        );

        $this->assertSame('completed', $campaign->status);
        $this->assertSame(2, $campaign->processed_count);
    }
}
