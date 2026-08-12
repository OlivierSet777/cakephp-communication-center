<?php
/**
 * @var \Cake\View\View $this
 * @var array<int, \CommunicationCenter\Channel\ChannelAction> $actions
 * @var array<string, \CommunicationCenter\Recipient\Recipient> $recipientsById
 * @var string $message
 * @var string $providerName
 * @var \CommunicationCenter\Model\Entity\CommunicationCampaign $campaign
 */
?>

<?= $this->Html->css(
    'CommunicationCenter.communication-center',
    ['block' => true],
) ?>

<div class="container py-4 communication-center">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="mb-4">
                <h1 class="h3 mb-2 communication-title">
                    Messages préparés
                </h1>

                <p class="text-muted communication-muted">
                    <?= count($actions) ?>
                    message<?= count($actions) > 1 ? 's' : '' ?>
                    WhatsApp prêt<?= count($actions) > 1 ? 's' : '' ?>.
                </p>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                <span class="communication-status communication-status--neutral" id="processed-count">
                    0 / <?= count($actions) ?> traité
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="next-action">
                    Suivant
                </button>
            </div>
            <div class="progress mb-4">
                <div class="communication-progress-bar" id="processed-progress" style="width: 0%"></div>
            </div>
            <div class="d-flex flex-column gap-3 mb-4">

                <?php foreach ($actions as $action): ?>
                    <?php
                    $recipient = $action->recipientId !== null
                        ? ($recipientsById[$action->recipientId] ?? null)
                        : null;
                    ?>

                    <?php if ($recipient === null): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div
                        class="card shadow-sm communication-action"
                        data-recipient-id="<?= h($action->recipientId ?? '') ?>"
                        data-campaign-id="<?= h((string)$campaign->id) ?>"
                    >

                        <div class="card-body p-3 p-md-4">

                        <div
                            class="row g-3 align-items-start"
                        >
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <div class="fw-semibold communication-recipient-name">
                                        <?= h(trim(
                                            ($recipient->firstname ?? '')
                                            . ' '
                                            . ($recipient->lastname ?? ''),
                                        )) ?>
                                    </div>
                                    <span class="communication-status communication-status--pending status-badge">À traiter</span>
                                </div>

                                <?php if ($recipient->phone !== null): ?>
                                    <small class="text-muted communication-muted">
                                        <?= h($recipient->phone) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <?php if ($action->message !== null): ?>
                                    <div class="p-3 rounded communication-message-preview">
                                        <?= nl2br(h($action->message)) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($action->url !== null): ?>
                                    <a href="<?= h($action->url) ?>" class="btn btn-communication-success whatsapp-button" target="_blank" rel="noopener noreferrer">
                                        Ouvrir WhatsApp
                                    </a>
                                <?php endif; ?>

                                <button type="button" class="btn btn-outline-communication-success mark-processed-button">
                                    Marquer comme traité
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <?= $this->Html->link(
                'Retour aux destinataires',
                [
                    'action' => 'index',
                    '?' => [
                        'provider' => $providerName,
                    ],
                ],
                [
                    'class' => 'communication-button communication-button--outline-secondary',
                ],
            ) ?>

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
        }
    };

    const setProcessedState = (card) => {
        const badge = card.querySelector('.status-badge');
        const button = card.querySelector('.mark-processed-button');

        card.dataset.processed = 'true';
        card.classList.add('communication-action--processed');

        if (badge) {
            badge.className =
                'communication-status status-badge communication-status--processed';

            badge.textContent = 'Traité';
        }

        if (button) {
            button.disabled = true;
            button.classList.remove('btn-outline-communication-success');
            button.classList.add('btn-communication-success');
            button.textContent = 'Traité';
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
        card.dataset.processed = 'false';

        const button = card.querySelector(
            '.mark-processed-button'
        );

        button?.addEventListener('click', async () => {
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

                goToNext();
            } catch (error) {
                button.disabled = false;
                button.textContent =
                    'Marquer comme traité';

                alert(
                    'Impossible de mettre à jour le destinataire.'
                );
            }
        });
    });

    nextButton?.addEventListener('click', goToNext);

    updateProgress(
        <?= (int)$campaign->processed_count ?>,
        <?= (int)$campaign->recipients_count ?>,
    );
});
</script>
