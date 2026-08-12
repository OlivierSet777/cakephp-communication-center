<?php
declare(strict_types=1);

namespace CommunicationCenter\Service;

use Cake\ORM\Query\SelectQuery;
use CommunicationCenter\Model\Entity\CommunicationCampaign;
use CommunicationCenter\Model\Table\CommunicationCampaignsTable;
use DateTimeImmutable;
use RuntimeException;

/**
 * Persists communication campaigns and their recipients.
 */
class CampaignService
{
    /**
     * Constructor.
     *
     * @param \CommunicationCenter\Model\Table\CommunicationCampaignsTable $campaigns
     *   Communication campaigns table.
     */
    public function __construct(
        private readonly CommunicationCampaignsTable $campaigns,
    ) {
    }

    /**
     * Get communication campaign dashboard statistics.
     *
     * @return array{
     *     campaigns: int,
     *     processing: int,
     *     completed: int,
     *     archived: int
     * }
     */
    public function getDashboardStats(): array
    {
        $campaigns = $this->campaigns;

        return [
            'campaigns' => $campaigns
                ->find()
                ->where([
                    'CommunicationCampaigns.archived' => false,
                ])
                ->count(),

            'processing' => $campaigns
                ->find()
                ->where([
                    'CommunicationCampaigns.archived' => false,
                    'CommunicationCampaigns.status' => 'processing',
                ])
                ->count(),

            'completed' => $campaigns
                ->find()
                ->where([
                    'CommunicationCampaigns.archived' => false,
                    'CommunicationCampaigns.status' => 'completed',
                ])
                ->count(),

            'archived' => $campaigns
                ->find()
                ->where([
                    'CommunicationCampaigns.archived' => true,
                ])
                ->count(),
        ];
    }

    /**
     * Archive a communication campaign.
     *
     * @param int $campaignId Campaign identifier.
     * @return \CommunicationCenter\Model\Entity\CommunicationCampaign
     * @throws \RuntimeException When the campaign cannot be archived.
     */
    public function archiveCampaign(
        int $campaignId,
    ): CommunicationCampaign {
        $campaign = $this->campaigns->get($campaignId);

        if ($campaign->archived) {
            return $campaign;
        }

        $campaign->archived = true;

        if (!$this->campaigns->save($campaign)) {
            throw new RuntimeException(
                'Unable to archive communication campaign.',
            );
        }

        return $campaign;
    }

    /**
     * Get a campaign with its recipients.
     *
     * @param int $id Campaign identifier.
     * @return \CommunicationCenter\Model\Entity\CommunicationCampaign
     */
    public function getCampaign(int $id): CommunicationCampaign
    {
        return $this->campaigns->get(
            $id,
            contain: ['CommunicationRecipients'],
        );
    }

    /**
     * Get communication campaigns query.
     *
     * @param string|null $status Campaign status.
     * @param string|null $channel Communication channel.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function getCampaignsQuery(
        ?string $status = null,
        ?string $channel = null,
    ): SelectQuery {
        $query = $this->campaigns
            ->find()
            ->where([
                'CommunicationCampaigns.archived' => false,
            ])
            ->orderBy([
                'CommunicationCampaigns.created' => 'DESC',
            ]);

        if ($status !== null && $status !== '') {
            $query->where([
                'CommunicationCampaigns.status' => $status,
            ]);
        }

        if ($channel !== null && $channel !== '') {
            $query->where([
                'CommunicationCampaigns.channel' => $channel,
            ]);
        }

        return $query;
    }

    /**
     * Get recent communication campaigns.
     *
     * @param int $limit Maximum number of campaigns.
     * @param string|null $status Campaign status.
     * @param string|null $channel Communication channel.
     * @return array<int, \CommunicationCenter\Model\Entity\CommunicationCampaign>
     */
    public function getCampaigns(
        int $limit = 50,
        ?string $status = null,
        ?string $channel = null,
    ): array {
        return $this->getCampaignsQuery(
            status: $status,
            channel: $channel,
        )
            ->limit($limit)
            ->all()
            ->toList();
    }

    /**
     * Restore an archived communication campaign.
     *
     * @param int $campaignId Campaign identifier.
     * @return \CommunicationCenter\Model\Entity\CommunicationCampaign
     * @throws \RuntimeException When the campaign cannot be restored.
     */
    public function restoreCampaign(
        int $campaignId,
    ): CommunicationCampaign {
        $campaign = $this->campaigns->get($campaignId);

        if (!$campaign->archived) {
            return $campaign;
        }

        $campaign->archived = false;

        if (!$this->campaigns->save($campaign)) {
            throw new RuntimeException(
                'Unable to restore communication campaign.',
            );
        }

        return $campaign;
    }

    /**
     * Get archived communication campaigns query.
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function getArchivedCampaignsQuery(): SelectQuery
    {
        return $this->campaigns
            ->find()
            ->where([
                'CommunicationCampaigns.archived' => true,
            ])
            ->orderBy([
                'CommunicationCampaigns.created' => 'DESC',
            ]);
    }

    /**
     * Create and persist a communication campaign.
     *
     * @param string $name Campaign name.
     * @param string $provider Recipient provider name.
     * @param string $channel Communication channel name.
     * @param string $messageTemplate Original message template.
     * @param array<int, array<string, mixed>> $recipients Recipient snapshots.
     * @param string|null $subject Email subject (optional).
     * @return \CommunicationCenter\Model\Entity\CommunicationCampaign
     */
    public function create(
        string $name,
        string $provider,
        string $channel,
        string $messageTemplate,
        array $recipients,
        ?string $subject = null,
    ): CommunicationCampaign {
        $connection = $this->campaigns->getConnection();

        return $connection->transactional(
            function () use (
                $name,
                $provider,
                $channel,
                $messageTemplate,
                $recipients,
                $subject,
            ): CommunicationCampaign {
                $campaign = $this->campaigns->newEntity([
                    'name' => $name,
                    'provider' => $provider,
                    'channel' => $channel,
                    'subject' => $subject,
                    'message_template' => $messageTemplate,
                    'status' => 'ready',
                    'archived' => false,
                    'recipients_count' => count($recipients),
                    'processed_count' => 0,
                    'communication_recipients' => $recipients,
                ], [
                    'associated' => ['CommunicationRecipients'],
                ]);

                if ($campaign->hasErrors()) {
                    throw new RuntimeException(
                        'Unable to create communication campaign.',
                    );
                }

                $saved = $this->campaigns->save($campaign, [
                    'associated' => ['CommunicationRecipients'],
                ]);

                if ($saved === false) {
                    throw new RuntimeException(
                        'Unable to save communication campaign.',
                    );
                }

                return $saved;
            },
        );
    }

    /**
     * Mark a campaign recipient as processed.
     *
     * @param int $campaignId Campaign identifier.
     * @param string $externalId Recipient external identifier.
     * @return \CommunicationCenter\Model\Entity\CommunicationCampaign
     */
    public function markRecipientProcessed(
        int $campaignId,
        string $externalId,
    ): CommunicationCampaign {
        $connection = $this->campaigns->getConnection();

        return $connection->transactional(
            function () use ($campaignId, $externalId): CommunicationCampaign {
                $campaign = $this->campaigns->get(
                    $campaignId,
                    contain: ['CommunicationRecipients'],
                );

                $recipient = null;

                foreach ($campaign->communication_recipients as $candidate) {
                    if ($candidate->external_id === $externalId) {
                        $recipient = $candidate;
                        break;
                    }
                }

                if ($recipient === null) {
                    throw new RuntimeException(
                        'Recipient does not belong to this campaign.',
                    );
                }

                if ($recipient->status === 'processed') {
                    return $campaign;
                }

                $recipient->status = 'processed';
                $recipient->processed = new DateTimeImmutable();

                $recipients = $this->campaigns
                    ->CommunicationRecipients;

                if (!$recipients->save($recipient)) {
                    throw new RuntimeException(
                        'Unable to mark recipient as processed.',
                    );
                }

                $campaign->processed_count++;

                if ($campaign->processed_count >= $campaign->recipients_count) {
                    $campaign->status = 'completed';
                } else {
                    $campaign->status = 'processing';
                }

                if (!$this->campaigns->save($campaign)) {
                    throw new RuntimeException(
                        'Unable to update communication campaign.',
                    );
                }

                return $campaign;
            },
        );
    }
}
