<?php
declare(strict_types=1);

namespace CommunicationCenter\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use CommunicationCenter\Channel\Registry\ChannelRegistry;
use CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry;
use CommunicationCenter\Service\CommunicationService;

/**
 * Communication Center Controller.
 */
class CommunicationCentersController extends Controller
{
    /**
     * Prepares communication actions for selected recipients.
     *
     * @param \CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry $providers Recipient providers.
     * @param \CommunicationCenter\Channel\Registry\ChannelRegistry $channels Communication channels.
     * @param \CommunicationCenter\Service\CommunicationService $communicationService Communication service.
     * @return \Cake\Http\Response|null
     */
    public function prepare(
        RecipientProviderRegistry $providers,
        ChannelRegistry $channels,
        CommunicationService $communicationService,
    ): ?Response {
        $providerName = $this->request->getData('provider');
        $selectedIds = $this->request->getData('recipients');
        $message = $this->request->getData('message');

        if (
            !is_string($providerName)
            || !$providers->has($providerName)
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

        $selectedIds = array_map(
            'strval',
            $selectedIds,
        );

        $recipients = [];

        foreach ($providers->get($providerName)->getRecipients() as $recipient) {
            if (in_array($recipient->externalId, $selectedIds, true)) {
                $recipients[] = $recipient;
            }
        }

        $actions = $communicationService->prepare(
            $recipients,
            $message,
            $channels->get('whatsapp'),
        );

        $recipientsById = [];

        foreach ($recipients as $recipient) {
            $recipientsById[$recipient->externalId] = $recipient;
        }

        $this->set(compact(
            'actions',
            'message',
            'providerName',
            'recipientsById',
        ));

        return null;
    }

    /**
     * Communication Center dashboard.
     *
     * @param \CommunicationCenter\Recipient\Provider\Registry\RecipientProviderRegistry $providers Recipient providers.
     * @param \CommunicationCenter\Channel\Registry\ChannelRegistry $channels Communication channels.
     * @return void
     */
    public function index(
        RecipientProviderRegistry $providers,
        ChannelRegistry $channels,
    ): void {
        $providerName = $this->request->getQuery('provider');

        $recipients = [];

        if (
            is_string($providerName)
            && $providerName !== ''
            && $providers->has($providerName)
        ) {
            $recipients = iterator_to_array(
                $providers->get($providerName)->getRecipients(),
                false,
            );
        }

        $whatsApp = $channels->get('whatsapp');

        $this->set([
            'providerName' => $providerName,
            'providers' => $providers->all(),
            'recipients' => $recipients,
            'whatsApp' => $whatsApp,
        ]);
    }
}
