<?php
/**
 * @var \Cake\View\View $this
 * @var array<int, \CommunicationCenter\Model\Entity\CommunicationCampaign> $campaigns
 * @var string|null $status
 * @var string|null $channel
 * @var object $campaigns
 */
?>

<?= $this->Html->css(
    'CommunicationCenter.communication-center',
    ['block' => true],
) ?>


<div class="container py-4 communication-center">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div
                class="d-flex flex-column flex-sm-row
                    justify-content-between align-items-sm-center
                    gap-3 mb-4"
            >
                <div>
                    <h1 class="h3 mb-2 communication-title">
                        Campagnes
                    </h1>

                    <p class="text-muted mb-0 communication-muted">
                        Retrouvez et reprenez vos campagnes de communication.
                    </p>
                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <?= $this->Html->link(
                    'Archives',
                    [
                        'action' => 'archives',
                    ],
                    [
                        'class' => 'btn btn-outline-secondary',
                    ],
                ) ?>

                <?= $this->Html->link(
                    'Nouvelle campagne',
                    [
                        'action' => 'index',
                    ],
                    [
                        'class' => 'btn btn-communication-success',
                    ],
                ) ?>
            </div>

            <?= $this->Form->create(null, [
                'type' => 'get',
                'class' => 'row g-2 mb-4',
            ]) ?>

            <div class="col-12 col-sm-5">
                <?= $this->Form->control('status', [
                    'label' => false,
                    'class' => 'form-select',
                    'empty' => 'Tous les statuts',
                    'options' => [
                        'ready' => 'Prête',
                        'processing' => 'En cours',
                        'completed' => 'Terminée',
                    ],
                    'value' => $status,
                ]) ?>
            </div>

            <div class="col-12 col-sm-5">
                <?= $this->Form->control('channel', [
                    'label' => false,
                    'class' => 'form-select',
                    'empty' => 'Tous les canaux',
                    'options' => [
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                    ],
                    'value' => $channel,
                ]) ?>
            </div>

            <div class="col-12 col-sm-2 d-grid">
                <button
                    type="submit"
                    class="btn btn-outline-secondary"
                >
                    Filtrer
                </button>
            </div>

            <?= $this->Form->end() ?>

            <?php if ($campaigns->items()->isEmpty()): ?>

                <div class="alert alert-info">
                    Aucune campagne n'a encore été créée.
                </div>

            <?php else: ?>

                <div class="d-flex flex-column gap-3">

                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                        $statusClass = match ($campaign->status) {
                            'completed' => 'communication-status--processed',
                            'processing' => 'communication-status--warning',
                            default => 'communication-status--neutral',
                        };

                        $actionLabel = match ($campaign->status) {
                            'completed' => 'Voir',
                            'processing' => 'Reprendre',
                            default => 'Commencer',
                        };

                        $statusLabel = match ($campaign->status) {
                            'ready' => 'Prête',
                            'processing' => 'En cours',
                            'completed' => 'Terminée',
                            default => $campaign->status,
                        };

                        $percentage = $campaign->recipients_count > 0
                            ? (int)round(
                                ($campaign->processed_count / $campaign->recipients_count) * 100,
                            )
                            : 0;
                        ?>

                        <div class="card shadow-sm">
                            <div class="card-body p-3 p-md-4">

                                <div class="row g-3 align-items-center">

                                    <div class="col-12 col-md">
                                        <div
                                            class="d-flex flex-wrap
                                                align-items-center gap-2 mb-2"
                                        >
                                            <h2 class="h5 mb-0">
                                                <?= h($campaign->name) ?>
                                            </h2>

                                            <span class="communication-status <?= $statusClass ?>">
                                                <?= h($statusLabel) ?>
                                            </span>
                                        </div>

                                        <div class="text-muted small mb-2">
                                            <?= h(ucfirst($campaign->channel)) ?>
                                            ·
                                            <?= (int)$campaign->processed_count ?>
                                            /
                                            <?= (int)$campaign->recipients_count ?>
                                            traité<?= $campaign->processed_count > 1 ? 's' : '' ?>
                                        </div>

                                        <?php if ($campaign->created !== null): ?>
                                            <div class="text-muted small mb-3">
                                                Créée le
                                                <?= h(
                                                    $campaign->created
                                                        ->i18nFormat('d MMMM yyyy à HH:mm'),
                                                ) ?>
                                            </div>
                                        <?php endif; ?>

                                        <div
                                            class="progress"
                                            role="progressbar"
                                            aria-label="Progression de la campagne"
                                            aria-valuenow="<?= $percentage ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        >
                                            <div
                                                class="progress-bar communication-progress-bar"
                                                style="width: <?= $percentage ?>%"
                                            >
                                            </div>
                                        </div>

                                        <div class="text-end text-muted small mt-1">
                                            <?= $percentage ?> %
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2">

                                            <?= $this->Html->link(
                                                $actionLabel,
                                                [
                                                    'action' => 'campaign',
                                                    $campaign->id,
                                                ],
                                                [
                                                    'class' => 'btn btn-outline-secondary',
                                                ],
                                            ) ?>

                                            <?php if ($campaign->status === 'completed'): ?>
                                                <?= $this->Form->postLink(
                                                    'Archiver',
                                                    [
                                                        'action' => 'archiveCampaign',
                                                        $campaign->id,
                                                    ],
                                                    [
                                                        'class' => 'btn btn-outline-danger',
                                                        'confirm' => sprintf(
                                                            'Archiver la campagne « %s » ?',
                                                            $campaign->name,
                                                        ),
                                                    ],
                                                ) ?>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>
</div>
<?php if ($campaigns->pageCount() > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?= $this->Paginator->first('«') ?>
            <?= $this->Paginator->prev('‹') ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next('›') ?>
            <?= $this->Paginator->last('»') ?>
        </ul>
    </nav>
<?php endif; ?>
