<?php
/**
 * @var \Cake\View\View $this
 * @var \Cake\Datasource\Paging\PaginatedResultSet $campaigns
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
                        Campagnes archivées
                    </h1>

                    <p class="text-muted mb-0 communication-muted">
                        Retrouvez les campagnes archivées et restaurez-les
                        si nécessaire.
                    </p>
                </div>

                <?= $this->Html->link(
                    'Retour aux campagnes',
                    [
                        'action' => 'campaigns',
                    ],
                    [
                        'class' => 'btn btn-outline-secondary',
                    ],
                ) ?>
            </div>

            <?php if ($campaigns->items()->isEmpty()): ?>

                <div class="alert alert-info">
                    Aucune campagne archivée.
                </div>

            <?php else: ?>

                <div class="d-flex flex-column gap-3">

                    <?php foreach ($campaigns as $campaign): ?>
                        <?php
                        $percentage = $campaign->recipients_count > 0
                            ? (int)round(
                                (
                                    $campaign->processed_count
                                    / $campaign->recipients_count
                                ) * 100,
                            )
                            : 0;

                        $statusLabel = match ($campaign->status) {
                            'ready' => 'Prête',
                            'processing' => 'En cours',
                            'completed' => 'Terminée',
                            default => $campaign->status,
                        };
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

                                            <span
                                                class="communication-status
                                                    communication-status--neutral"
                                            >
                                                Archivée
                                            </span>

                                            <span class="text-muted small">
                                                <?= h($statusLabel) ?>
                                            </span>
                                        </div>

                                        <div class="text-muted small mb-2">
                                            <?= h(ucfirst($campaign->channel)) ?>
                                            ·
                                            <?= (int)$campaign->processed_count ?>
                                            /
                                            <?= (int)$campaign->recipients_count ?>
                                            traité<?= $campaign->processed_count > 1
                                                ? 's'
                                                : '' ?>
                                        </div>

                                        <?php if ($campaign->created !== null): ?>
                                            <div class="text-muted small mb-3">
                                                Créée le
                                                <?= h(
                                                    $campaign->created
                                                        ->i18nFormat(
                                                            'd MMMM yyyy à HH:mm',
                                                        ),
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
                                                class="progress-bar
                                                    communication-progress-bar"
                                                style="width: <?= $percentage ?>%"
                                            ></div>
                                        </div>

                                        <div
                                            class="text-end text-muted
                                                small mt-1"
                                        >
                                            <?= $percentage ?> %
                                        </div>

                                    </div>

                                    <div class="col-12 col-md-auto">
                                        <div
                                            class="d-flex flex-column
                                                flex-sm-row gap-2"
                                        >
                                            <?= $this->Html->link(
                                                'Voir',
                                                [
                                                    'action' => 'campaign',
                                                    $campaign->id,
                                                ],
                                                [
                                                    'class' =>
                                                        'btn btn-outline-secondary',
                                                ],
                                            ) ?>

                                            <?= $this->Form->postLink(
                                                'Restaurer',
                                                [
                                                    'action' => 'restoreCampaign',
                                                    $campaign->id,
                                                ],
                                                [
                                                    'class' =>
                                                        'btn btn-outline-success',
                                                    'confirm' => sprintf(
                                                        'Restaurer la campagne « %s » ?',
                                                        $campaign->name,
                                                    ),
                                                ],
                                            ) ?>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <?php if ($campaigns->pageCount() > 1): ?>
                <nav class="mt-4" aria-label="Pagination des archives">
                    <ul class="pagination justify-content-center">
                        <?= $this->Paginator->first('«') ?>
                        <?= $this->Paginator->prev('‹') ?>
                        <?= $this->Paginator->numbers() ?>
                        <?= $this->Paginator->next('›') ?>
                        <?= $this->Paginator->last('»') ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>
