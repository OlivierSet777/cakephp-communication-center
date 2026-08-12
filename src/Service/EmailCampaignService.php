<?php
declare(strict_types=1);

namespace CommunicationCenter\Service;

use CommunicationCenter\Email\EmailSenderInterface;
use RuntimeException;

/**
 * Sends emails for communication campaigns.
 */
final readonly class EmailCampaignService
{
    /**
     * Constructor.
     *
     * @param \CommunicationCenter\Service\CampaignService $campaignService Campaign service.
     * @param \CommunicationCenter\Email\EmailSenderInterface $emailSender Email sender.
     */
    public function __construct(
        private CampaignService $campaignService,
        private EmailSenderInterface $emailSender,
    ) {
    }

    /**
     * Send an email to one campaign recipient.
     *
     * @param int $campaignId Campaign identifier.
     * @param string $externalId Recipient external identifier.
     * @return void
     */
    public function send(
        int $campaignId,
        string $externalId,
    ): void {
        $campaign = $this->campaignService->getCampaign(
            $campaignId,
        );

        if ($campaign->channel !== 'email') {
            throw new RuntimeException(
                'This campaign is not an email campaign.',
            );
        }

        if (
            $campaign->subject === null
            || trim($campaign->subject) === ''
        ) {
            throw new RuntimeException(
                'Email campaign subject is missing.',
            );
        }

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
            return;
        }

        if (
            $recipient->email === null
            || filter_var(
                $recipient->email,
                FILTER_VALIDATE_EMAIL,
            ) === false
        ) {
            throw new RuntimeException(
                'Recipient email address is invalid.',
            );
        }

        $this->emailSender->send(
            $recipient->email,
            $campaign->subject,
            $recipient->rendered_message,
        );

        $this->campaignService->markRecipientProcessed(
            $campaignId,
            $externalId,
        );
    }

    /**
     * Send emails to all pending campaign recipients.
     *
     * @param int $campaignId Campaign identifier.
     * @return array{
     *     sent: int,
     *     failed: int
     * }
     */
    public function sendAll(
        int $campaignId,
    ): array {
        $campaign = $this->campaignService->getCampaign(
            $campaignId,
        );

        if ($campaign->channel !== 'email') {
            throw new RuntimeException(
                'This campaign is not an email campaign.',
            );
        }

        $sent = 0;
        $failed = 0;

        foreach ($campaign->communication_recipients as $recipient) {
            if ($recipient->status === 'processed') {
                continue;
            }

            try {
                $this->send(
                    $campaignId,
                    $recipient->external_id,
                );

                $sent++;
            } catch (RuntimeException) {
                $failed++;
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
        ];
    }
}
