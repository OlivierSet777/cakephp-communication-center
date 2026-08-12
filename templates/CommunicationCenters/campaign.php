<?php
/**
 * @var \Cake\View\View $this
 * @var \CommunicationCenter\Model\Entity\CommunicationCampaign $campaign
 * @var array<string, \CommunicationCenter\Channel\ChannelAction> $actions
 */
?>

<?= $this->Html->css(
    'CommunicationCenter.communication-center',
    ['block' => true],
) ?>

<?php
$channelActionLabel = match ($campaign->channel) {
    'whatsapp' => 'Ouvrir WhatsApp',
    default => 'Ouvrir',
};
?>

<div class="container py-4 communication-center">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="mb-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3">
                    <div>
                        <h1 class="h3 mb-1 communication-title">
                            <?= h($campaign->name) ?>
                        </h1>

                        <div class="text-muted communication-muted">
                            Campagne #<?= h((string)$campaign->id) ?>
                            · <?= h($campaign->channel) ?>
                        </div>
                        <?php if (
                            $campaign->channel === 'email'
                            && !empty($campaign->subject)
                        ): ?>
                            <div class="mb-3">
                                <div class="small communication-muted">
                                    Objet
                                </div>

                                <div class="fw-semibold">
                                    <?= h($campaign->subject) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $statusClass = match ($campaign->status) {
                        'completed' => 'communication-status--processed',
                        'processing' => 'communication-status--warning',
                        default => 'communication-status--neutral',
                    };
                    ?>

                    <span class="communication-status communication-status--campaign <?= $statusClass ?>">
                        <?= h($campaign->status) ?>
                    </span>
                </div>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <span
                    class="communication-status communication-status--neutral align-self-start align-self-sm-auto"
                    id="processed-count"
                >
                    <?= (int)$campaign->processed_count ?>
                    /
                    <?= (int)$campaign->recipients_count ?>
                    traité<?= $campaign->processed_count > 1 ? 's' : '' ?>
                </span>

                <div class="d-flex flex-wrap gap-2">

                    <?php if (
                        $campaign->channel === 'email'
                        && $campaign->status !== 'completed'
                    ): ?>
                        <?= $this->Form->create(null, [
                            'url' => [
                                'plugin' => 'CommunicationCenter',
                                'controller' => 'CommunicationCenters',
                                'action' => 'sendAllEmails',
                            ],
                        ]) ?>

                        <?= $this->Form->hidden('campaign_id', [
                            'value' => $campaign->id,
                        ]) ?>

                        <?= $this->Form->button(
                            'Envoyer tous les emails',
                            [
                                'type' => 'submit',
                                'class' => 'btn btn-sm btn-communication-success',
                                'confirm' => 'Envoyer tous les emails encore en attente ?',
                            ],
                        ) ?>

                        <?= $this->Form->end() ?>
                    <?php endif; ?>

                    <?php if ($campaign->status !== 'completed'): ?>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="next-action"
                        >
                            Suivant
                        </button>
                    <?php endif; ?>

                </div>
            </div>

            <?php
            $percentage = $campaign->recipients_count > 0
                ? (int)round(
                    ($campaign->processed_count / $campaign->recipients_count) * 100,
                )
                : 0;
            ?>

            <div
                class="progress mb-4"
                role="progressbar"
                aria-label="Progression"
                aria-valuemin="0"
                aria-valuemax="<?= (int)$campaign->recipients_count ?>"
                aria-valuenow="<?= (int)$campaign->processed_count ?>"
            >
                <div
                    class="progress-bar communication-progress-bar"
                    id="processed-progress"
                    style="width: <?= $percentage ?>%"
                ></div>
            </div>

            <div class="d-flex flex-column gap-3 mb-4">

                <?php foreach ($campaign->communication_recipients as $recipient): ?>
                    <?php
                    $processed = $recipient->status === 'processed';
                    $action = $actions[$recipient->external_id] ?? null;
                    ?>

                    <div
                        class="card shadow-sm communication-action
                            <?= $processed ? 'communication-action--processed' : '' ?>"
                        data-recipient-id="<?= h($recipient->external_id) ?>"
                        data-campaign-id="<?= h((string)$campaign->id) ?>"
                        data-processed="<?= $processed ? 'true' : 'false' ?>"
                    >
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3 align-items-start">

                                <div class="col-12 col-md">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <div class="fw-semibold communication-recipient-name">
                                            <?= h(trim(
                                                ($recipient->firstname ?? '')
                                                . ' '
                                                . ($recipient->lastname ?? ''),
                                            )) ?>
                                        </div>

                                        <span
                                            class="communication-status status-badge
                                                <?= $processed
                                                    ? 'communication-status--processed'
                                                    : 'communication-status--pending' ?>"
                                        >
                                            <?= $processed ? 'Traité' : 'À traiter' ?>
                                        </span>
                                    </div>

                                    <?php if ($recipient->phone !== null): ?>
                                        <small class="text-muted communication-muted">
                                            <?= h($recipient->phone) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12 col-md-5">
                                    <div class="d-grid gap-2">
                                        <div class="p-3 rounded communication-message-preview">
                                            <?= nl2br(h($recipient->rendered_message)) ?>
                                        </div>

                                        <?php if (!$processed): ?>

                                            <?php if ($campaign->channel === 'email'): ?>

                                                <?= $this->Form->create(null, [
                                                    'url' => [
                                                        'plugin' => 'CommunicationCenter',
                                                        'controller' => 'CommunicationCenters',
                                                        'action' => 'sendEmail',
                                                    ],
                                                ]) ?>

                                                <?= $this->Form->hidden('campaign_id', [
                                                    'value' => $campaign->id,
                                                ]) ?>

                                                <?= $this->Form->hidden('external_id', [
                                                    'value' => $recipient->external_id,
                                                ]) ?>

                                                <?= $this->Form->button(
                                                    'Envoyer l’email',
                                                    [
                                                        'type' => 'submit',
                                                        'class' => 'btn btn-communication-success channel-action-button w-100',
                                                        'confirm' => sprintf(
                                                            'Envoyer cet email à %s ?',
                                                            $recipient->email ?? 'ce destinataire',
                                                        ),
                                                    ],
                                                ) ?>

                                                <?= $this->Form->end() ?>

                                            <?php elseif (
                                                $action !== null
                                                && $action->url !== null
                                            ): ?>

                                                <a
                                                    href="<?= h($action->url) ?>"
                                                    class="btn btn-communication-success channel-action-button"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    <?= h($channelActionLabel) ?>
                                                </a>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                        <button
                                            type="button"
                                            class="btn <?= $processed
                                                ? 'btn-communication-success'
                                                : 'btn-outline-communication-success' ?>
                                                mark-processed-button"
                                            <?= $processed ? 'disabled' : '' ?>
                                        >
                                            <?= $processed ? 'Traité' : 'Marquer comme traité' ?>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(
        document.querySelectorAll('.communication-action')
    );

    const counter = document.getElementById('processed-count');
    const progress = document.getElementById('processed-progress');
    const nextButton = document.getElementById('next-action');

    const csrfToken = <?= json_encode(
        $this->getRequest()->getAttribute('csrfToken'),
        JSON_THROW_ON_ERROR,
    ) ?>;

    const processUrl = <?= json_encode(
        $this->Url->build([
            'plugin' => 'CommunicationCenter',
            'controller' => 'CommunicationCenters',
            'action' => 'processRecipient',
        ]),
        JSON_THROW_ON_ERROR,
    ) ?>;

    const updateProgress = (processed, total) => {
        const percentage = total > 0
            ? Math.round((processed / total) * 100)
            : 0;

        if (counter) {
            counter.textContent =
                `${processed} / ${total} traité` +
                (processed > 1 ? 's' : '');
        }

        if (progress) {
            progress.style.width = `${percentage}%`;

            progress.parentElement?.setAttribute(
                'aria-valuenow',
                String(processed),
            );
        }
    };

    const setProcessedState = (card) => {
        const badge = card.querySelector('.status-badge');
        const button = card.querySelector('.mark-processed-button');
        const channelActionButton = card.querySelector('.channel-action-button');

        card.dataset.processed = 'true';
        card.classList.add('communication-action--processed');

        if (badge) {
            badge.className =
                'communication-status status-badge ' +
                'communication-status--processed';

            badge.textContent = 'Traité';
        }

        if (button) {
            button.disabled = true;
            button.classList.remove(
                'btn-outline-communication-success'
            );
            button.classList.add('btn-communication-success');
            button.textContent = 'Traité';
        }

        if (channelActionButton) {
            channelActionButton.remove();
        }
    };

    const goToNext = () => {
        const nextCard = cards.find(
            (card) => card.dataset.processed !== 'true'
        );

        if (!nextCard) {
            return;
        }

        nextCard.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    };

    cards.forEach((card) => {
        const button = card.querySelector('.mark-processed-button');

        if (!button || card.dataset.processed === 'true') {
            return;
        }

        button.addEventListener('click', async () => {
            const campaignId = card.dataset.campaignId;
            const recipientId = card.dataset.recipientId;

            if (!campaignId || !recipientId) {
                return;
            }

            button.disabled = true;
            button.textContent = 'Traitement...';

            const body = new FormData();

            body.append('campaign_id', campaignId);
            body.append('external_id', recipientId);

            try {
                const response = await fetch(processUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message ?? 'Erreur inconnue.'
                    );
                }

                setProcessedState(card);

                updateProgress(
                    data.campaign.processed_count,
                    data.campaign.recipients_count,
                );

                if (data.campaign.status === 'completed') {
                    nextButton?.remove();
                } else {
                    goToNext();
                }
            } catch (error) {
                button.disabled = false;
                button.textContent = 'Marquer comme traité';

                alert(
                    'Impossible de mettre à jour le destinataire.'
                );
            }
        });
    });

    nextButton?.addEventListener('click', goToNext);
});
</script>
