<?php
declare(strict_types=1);

namespace CommunicationCenter\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Model\Table\CommunicationTemplatesTable;
use CommunicationCenter\Recipient\Provider\FilterableRecipientProviderInterface;
use CommunicationCenter\Recipient\Provider\RecipientProviderInterface;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Recipient\Recipient;
use CommunicationCenter\Service\CampaignService;
use CommunicationCenter\Service\CommunicationService;
use CommunicationCenter\Service\EmailCampaignService;
use RuntimeException;

/**
 * Communication Center Controller.
 */
class CommunicationCentersController extends Controller
{
    /**
     * Initialize controller.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * Display communication campaign history.
     *
     * @param \CommunicationCenter\Service\CampaignService $campaignService
     *   Campaign service.
     * @return void
     */
    public function campaigns(
        CampaignService $campaignService,
    ): void {
        $status = $this->request->getQuery('status');
        $channel = $this->request->getQuery('channel');

        $query = $campaignService->getCampaignsQuery(
            status: is_string($status) ? $status : null,
            channel: is_string($channel) ? $channel : null,
        );

        $campaigns = $this->paginate($query, [
            'limit' => 10,
            'maxLimit' => 50,
        ]);

        $this->set(compact(
            'campaigns',
            'channel',
            'status',
        ));
    }

    /**
     * Displays a persisted communication campaign.
     *
     * @param int $id Campaign identifier.
     * @param \CommunicationCenter\Service\CampaignService $campaignService Campaign service.
     * @param \CommunicationCenter\Channel\Registry\ChannelRegistry $channels Communication channels.
     * @return void
     */
    public function campaign(
        int $id,
        CampaignService $campaignService,
        ChannelRegistry $channels,
    ): void {
        $campaign = $campaignService->getCampaign($id);

        $channel = $channels->get($campaign->channel);

        $actions = [];

        foreach ($campaign->communication_recipients as $recipient) {
            if ($recipient->status === 'processed') {
                continue;
            }

            $normalizedRecipient = new Recipient(
                externalId: $recipient->external_id,
                firstname: $recipient->firstname,
                lastname: $recipient->lastname,
                phone: $recipient->phone,
                email: $recipient->email,
            );

            if (!$channel->supports($normalizedRecipient)) {
                continue;
            }

            $actions[$recipient->external_id] = $channel->prepare(
                $normalizedRecipient,
                $recipient->rendered_message,
                [
                    'subject' => $campaign->subject,
                ],
            );
        }

        $recipientsById = [];

        foreach ($campaign->communication_recipients as $recipient) {
            $recipientsById[$recipient->external_id] = $recipient;
        }

        $this->set(compact(
            'actions',
            'campaign',
            'recipientsById',
        ));
    }

    /**
     * Display archived communication campaigns.
     *
     * @param \CommunicationCenter\Service\CampaignService $campaignService
     *   Campaign service.
     * @return void
     */
    public function archives(
        CampaignService $campaignService,
    ): void {
        $query = $campaignService->getArchivedCampaignsQuery();

        $campaigns = $this->paginate($query, [
            'limit' => 10,
            'maxLimit' => 50,
        ]);

        $this->set(compact('campaigns'));
    }

    /**
     * Restore an archived communication campaign.
     *
     * @param int $id Campaign identifier.
     * @param \CommunicationCenter\Service\CampaignService $campaignService
     *   Campaign service.
     * @return \Cake\Http\Response
     */
    public function restoreCampaign(
        int $id,
        CampaignService $campaignService,
    ): Response {
        $this->request->allowMethod(['post']);

        try {
            $campaignService->restoreCampaign($id);

            $this->Flash->success(
                'La campagne a été restaurée.',
            );
        } catch (RuntimeException) {
            $this->Flash->error(
                'Impossible de restaurer cette campagne.',
            );
        }

        return $this->redirect([
            'action' => 'archives',
        ]);
    }

    /**
     * Archive a communication campaign.
     *
     * @param int $id Campaign identifier.
     * @param \CommunicationCenter\Service\CampaignService $campaignService
     *   Campaign service.
     * @return \Cake\Http\Response
     */
    public function archiveCampaign(
        int $id,
        CampaignService $campaignService,
    ): Response {
        $this->request->allowMethod(['post']);

        try {
            $campaignService->archiveCampaign($id);

            $this->Flash->success(
                'La campagne a été archivée.',
            );
        } catch (RuntimeException) {
            $this->Flash->error(
                'Impossible d’archiver cette campagne.',
            );
        }

        return $this->redirect([
            'action' => 'campaigns',
        ]);
    }

    /**
     * Marks a campaign recipient as processed.
     *
     * @param \CommunicationCenter\Service\CampaignService $campaignService Campaign service.
     * @return \Cake\Http\Response
     */
    public function processRecipient(
        CampaignService $campaignService,
    ): Response {
        $this->request->allowMethod(['post']);

        $campaignId = $this->request->getData('campaign_id');
        $externalId = $this->request->getData('external_id');

        if (
            !is_numeric($campaignId)
            || !is_string($externalId)
            || $externalId === ''
        ) {
            return $this->response
                ->withType('application/json')
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Données invalides.',
                ], JSON_THROW_ON_ERROR));
        }

        try {
            $campaign = $campaignService->markRecipientProcessed(
                (int)$campaignId,
                $externalId,
            );
        } catch (RuntimeException) {
            return $this->response
                ->withType('application/json')
                ->withStatus(404)
                ->withStringBody(json_encode([
                    'success' => false,
                    'message' => 'Destinataire introuvable.',
                ], JSON_THROW_ON_ERROR));
        }

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'campaign' => [
                    'id' => $campaign->id,
                    'status' => $campaign->status,
                    'processed_count' => $campaign->processed_count,
                    'recipients_count' => $campaign->recipients_count,
                ],
            ], JSON_THROW_ON_ERROR));
    }

    /**
     * Sends an email to a campaign recipient.
     *
     * @param \CommunicationCenter\Service\EmailCampaignService $emailCampaignService
     *   Email campaign service.
     * @return \Cake\Http\Response
     */
    public function sendEmail(
        EmailCampaignService $emailCampaignService,
    ): Response {
        $this->request->allowMethod(['post']);

        $campaignId = $this->request->getData('campaign_id');
        $externalId = $this->request->getData('external_id');

        if (
            !is_numeric($campaignId)
            || !is_string($externalId)
            || $externalId === ''
        ) {
            $this->Flash->error(
                'Données invalides.',
            );

            return $this->redirect([
                'action' => 'campaigns',
            ]);
        }

        try {
            $emailCampaignService->send(
                (int)$campaignId,
                $externalId,
            );

            $this->Flash->success(
                'L’email a été envoyé avec succès.',
            );
        } catch (RuntimeException) {
            $this->Flash->error(
                'Impossible d’envoyer cet email.',
            );
        }

        return $this->redirect([
            'action' => 'campaign',
            (int)$campaignId,
        ]);
    }

    /**
     * Sends emails to all pending campaign recipients.
     *
     * @param \CommunicationCenter\Service\EmailCampaignService $emailCampaignService
     *   Email campaign service.
     * @return \Cake\Http\Response
     */
    public function sendAllEmails(
        EmailCampaignService $emailCampaignService,
    ): Response {
        $this->request->allowMethod(['post']);

        $campaignId = $this->request->getData('campaign_id');

        if (!is_numeric($campaignId)) {
            $this->Flash->error(
                'Campagne invalide.',
            );

            return $this->redirect([
                'action' => 'campaigns',
            ]);
        }

        try {
            $result = $emailCampaignService->sendAll(
                (int)$campaignId,
            );

            if ($result['failed'] === 0) {
                $this->Flash->success(
                    sprintf(
                        '%d email%s envoyé%s avec succès.',
                        $result['sent'],
                        $result['sent'] > 1 ? 's' : '',
                        $result['sent'] > 1 ? 's' : '',
                    ),
                );
            } else {
                $this->Flash->warning(
                    sprintf(
                        '%d email%s envoyé%s, %d échec%s.',
                        $result['sent'],
                        $result['sent'] > 1 ? 's' : '',
                        $result['sent'] > 1 ? 's' : '',
                        $result['failed'],
                        $result['failed'] > 1 ? 's' : '',
                    ),
                );
            }
        } catch (RuntimeException) {
            $this->Flash->error(
                'Impossible d’envoyer les emails de cette campagne.',
            );
        }

        return $this->redirect([
            'action' => 'campaign',
            (int)$campaignId,
        ]);
    }

    /**
     * Prepares communication actions for selected recipients.
     *
     * @param \CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry $providers Recipient providers.
     * @param \CommunicationCenter\Channel\Registry\ChannelRegistry $channels Communication channels.
     * @param \CommunicationCenter\Service\CommunicationService $communicationService Communication service.
     * @param \CommunicationCenter\Service\CampaignService $campaignService Campaign service.
     * @return \Cake\Http\Response|null
     */
    public function prepare(
        RecipientProviderRegistry $providers,
        ChannelRegistry $channels,
        CommunicationService $communicationService,
        CampaignService $campaignService,
    ): ?Response {
        $this->request->allowMethod(['post']);
        $providerName = $this->request->getData('provider');
        $channelName = $this->request->getData('channel');
        $selectedIds = $this->request->getData('recipients');
        $message = $this->request->getData('message');
        $subject = $this->request->getData('subject');

        if (!is_string($subject)) {
            $subject = null;
        }

        $subject = $subject !== null
            ? trim($subject)
            : null;

        $requestedCriteria = $this->request->getData('criteria');

        if (
            $channelName === 'email'
            && ($subject === null || $subject === '')
        ) {
            $this->Flash->error(
                'L’objet est obligatoire pour une campagne Email.',
            );

            return $this->redirect([
                'action' => 'index',
                '?' => [
                    'provider' => $providerName,
                    'channel' => $channelName,
                ],
            ]);
        }

        if (
            !is_string($providerName)
            || !$providers->has($providerName)
            || !is_string($channelName)
            || !$channels->has($channelName)
            || !is_array($selectedIds)
            || !is_string($message)
            || trim($message) === ''
        ) {
            $this->Flash->error(
                'Impossible de préparer les messages.',
            );

            return $this->redirect([
                'action' => 'index',
            ]);
        }

        $provider = $providers->get($providerName);

        $criteria = $this->resolveCriteria(
            $provider,
            $requestedCriteria,
        );

        $selectedIds = array_map(
            'strval',
            $selectedIds,
        );

        $recipients = [];

        foreach ($provider->getRecipients($criteria) as $recipient) {
            if (in_array($recipient->externalId, $selectedIds, true)) {
                $recipients[] = $recipient;
            }
        }

        $channel = $channels->get($channelName);

        $actions = $communicationService->prepare(
            $recipients,
            $message,
            $channel,
            [
                'subject' => $subject,
            ],
        );

        if ($actions === []) {
            $this->Flash->error(
                'Aucun destinataire compatible avec le canal sélectionné.',
            );

            return $this->redirect([
                'action' => 'index',
                '?' => [
                    'provider' => $providerName,
                    'channel' => $channelName,
                ],
            ]);
        }

        $snapshots = [];

        foreach ($actions as $action) {
            if ($action->recipientId === null) {
                continue;
            }

            $recipient = null;

            foreach ($recipients as $candidate) {
                if ($candidate->externalId === $action->recipientId) {
                    $recipient = $candidate;
                    break;
                }
            }

            if ($recipient === null) {
                continue;
            }

            $snapshots[] = [
                'external_id' => $recipient->externalId,
                'firstname' => $recipient->firstname,
                'lastname' => $recipient->lastname,
                'phone' => $recipient->phone,
                'email' => $recipient->email,
                'rendered_message' => $action->message ?? '',
                'status' => 'pending',
            ];
        }

        $campaign = $campaignService->create(
            name: 'Campagne du ' . date('d/m/Y H:i'),
            provider: $providerName,
            channel: $channelName,
            messageTemplate: $message,
            recipients: $snapshots,
            subject: $channelName === 'email'
                ? $subject
                : null,
        );

        return $this->redirect([
            'action' => 'campaign',
            $campaign->id,
        ]);
    }

    /**
     * Communication Center dashboard.
     *
     * @param \CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry $providers
     *   Recipient providers.
     * @param \CommunicationCenter\Channel\Registry\ChannelRegistry $channels
     *   Communication channels.
     * @param \CommunicationCenter\Service\CampaignService $campaignService
     *   Campaign service.
     * @param \CommunicationCenter\Model\Table\CommunicationTemplatesTable $templatesTable
     *   Communication templates table.
     * @return void
     */
    public function index(
        RecipientProviderRegistry $providers,
        ChannelRegistry $channels,
        CampaignService $campaignService,
        CommunicationTemplatesTable $templatesTable,
    ): void {
        $providerName = $this->request->getQuery('provider');

        $recipients = [];
        $filters = [];
        $criteria = [];

        if (
            is_string($providerName)
            && $providerName !== ''
            && $providers->has($providerName)
        ) {
            $provider = $providers->get($providerName);

            if ($provider instanceof FilterableRecipientProviderInterface) {
                $filters = $provider->getFilters();
            }

            $criteria = $this->resolveCriteria(
                $provider,
                $this->request->getQuery('criteria'),
            );

            $recipients = iterator_to_array(
                $provider->getRecipients($criteria),
                false,
            );
        }

        $channelName = $this->request->getQuery('channel');

        if (!is_string($channelName) || !$channels->has($channelName)) {
            $channelName = 'whatsapp';
        }

        $channel = $channels->get($channelName);

        $templates = $templatesTable
            ->find()
            ->where([
                'CommunicationTemplates.channel' => $channelName,
                'CommunicationTemplates.active' => true,
            ])
            ->orderBy([
                'CommunicationTemplates.name' => 'ASC',
            ])
            ->all()
            ->toList();

        $stats = $campaignService->getDashboardStats();

        $this->set([
            'providerName' => $providerName,
            'providers' => $providers->all(),
            'recipients' => $recipients,
            'channelName' => $channelName,
            'channel' => $channel,
            'channels' => $channels->all(),
            'filters' => $filters,
            'criteria' => $criteria,
            'templates' => $templates,
            'stats' => $stats,
        ]);
    }

    /**
     * Resolves and validates recipient provider criteria.
     *
     * @param \CommunicationCenter\Recipient\Provider\RecipientProviderInterface $provider
     *   Recipient provider.
     * @param mixed $requestedCriteria Requested criteria.
     * @return array<string, string>
     */
    private function resolveCriteria(
        RecipientProviderInterface $provider,
        mixed $requestedCriteria,
    ): array {
        if (
            !$provider instanceof FilterableRecipientProviderInterface
            || !is_array($requestedCriteria)
        ) {
            return [];
        }

        $filters = $provider->getFilters();
        $criteria = [];

        foreach ($filters as $name => $filter) {
            $value = $requestedCriteria[$name] ?? null;
            $options = $filter['options'] ?? [];

            if (
                is_string($value)
                && $value !== ''
                && is_array($options)
                && isset($options[$value])
            ) {
                $criteria[$name] = $value;
            }
        }

        return $criteria;
    }
}
