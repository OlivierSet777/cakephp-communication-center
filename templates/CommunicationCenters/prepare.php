<?php
/**
 * @var \Cake\View\View $this
 * @var array<int, \CommunicationCenter\Channel\ChannelAction> $actions
 * @var array<string, \CommunicationCenter\Recipient\Recipient> $recipientsById
 * @var string $message
 * @var string $providerName
 */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="mb-4">
                <h1 class="h3 mb-2">
                    Messages préparés
                </h1>

                <p class="text-muted">
                    <?= count($actions) ?>
                    message<?= count($actions) > 1 ? 's' : '' ?>
                    WhatsApp prêt<?= count($actions) > 1 ? 's' : '' ?>.
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge text-bg-secondary fs-6" id="processed-count">
                    0 / <?= count($actions) ?> traité
                </span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="next-action">
                    Suivant
                </button>
            </div>
            <div class="progress mb-4">
                <div class="progress-bar" id="processed-progress" style="width: 0%"></div>
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

                    <div class="card shadow-sm communication-action" data-recipient-id="<?= h($action->recipientId ?? '') ?>">
                        <div class="card-body p-3">

                        <div
                            class="d-flex flex-column flex-md-row
                                justify-content-between gap-3"
                        >
                            <div>
                                <div class="d-flex justify-content-between gap-2">
                                    <div class="fw-semibold">
                                        <?= h(trim(
                                            ($recipient->firstname ?? '')
                                            . ' '
                                            . ($recipient->lastname ?? ''),
                                        )) ?>
                                    </div>
                                    <span class="badge text-bg-secondary status-badge">À traiter</span>
                                </div>

                                <?php if ($recipient->phone !== null): ?>
                                    <small class="text-muted">
                                        <?= h($recipient->phone) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <?php if ($action->message !== null): ?>
                                    <div class="bg-body-tertiary rounded p-3">
                                        <?= nl2br(h($action->message)) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($action->url !== null): ?>
                                    <a href="<?= h($action->url) ?>" class="btn btn-success whatsapp-button" target="_blank" rel="noopener noreferrer">
                                        Ouvrir WhatsApp
                                    </a>
                                <?php endif; ?>

                                <button type="button" class="btn btn-outline-success mark-processed-button">
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
                    'class' => 'btn btn-outline-secondary',
                ],
            ) ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('.communication-action'));
    const counter = document.getElementById('processed-count');
    const progress = document.getElementById('processed-progress');
    const nextButton = document.getElementById('next-action');
    const storageKey = `communication-center:${window.location.pathname}:processed`;
    let processedIds = [];
    try {
        const stored = JSON.parse(sessionStorage.getItem(storageKey) ?? '[]');
        processedIds = Array.isArray(stored) ? stored : [];
    } catch (error) {
        processedIds = [];
    }
    const updateCard = (card) => {
        const processed = processedIds.includes(card.dataset.recipientId);
        const badge = card.querySelector('.status-badge');
        const button = card.querySelector('.mark-processed-button');
        card.classList.toggle('border-success', processed);
        card.classList.toggle('opacity-75', processed);
        if (badge) {
            badge.className = processed ? 'badge text-bg-success status-badge' : 'badge text-bg-secondary status-badge';
            badge.textContent = processed ? 'Traité' : 'À traiter';
        }
        if (button) {
            button.textContent = processed ? 'Marquer comme non traité' : 'Marquer comme traité';
        }
    };
    const updateProgress = () => {
        const processed = cards.filter((card) => processedIds.includes(card.dataset.recipientId)).length;
        const total = cards.length;
        if (counter) {
            counter.textContent = `${processed} / ${total} traité${processed > 1 ? 's' : ''}`;
        }
        if (progress) {
            progress.style.width = `${total > 0 ? (processed / total) * 100 : 0}%`;
        }
        sessionStorage.setItem(storageKey, JSON.stringify(processedIds));
    };
    const goToNext = () => {
        const card = cards.find((item) => !processedIds.includes(item.dataset.recipientId));
        card?.scrollIntoView({behavior: 'smooth', block: 'center'});
    };
    cards.forEach((card) => {
        updateCard(card);
        card.querySelector('.mark-processed-button')?.addEventListener('click', () => {
            const id = card.dataset.recipientId;
            processedIds = processedIds.includes(id)
                ? processedIds.filter((recipientId) => recipientId !== id)
                : [...processedIds, id];
            updateCard(card);
            updateProgress();
            goToNext();
        });
    });
    nextButton?.addEventListener('click', goToNext);
    updateProgress();
});
</script>
