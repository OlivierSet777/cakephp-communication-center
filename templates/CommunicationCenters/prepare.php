<?php
/**
 * @var \Cake\View\View $this
 * @var array $actions
 * @var array $recipients
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

            <div class="list-group mb-4">

                <?php foreach ($actions as $index => $action): ?>
                    <?php $recipient = $recipients[$index] ?? null; ?>

                    <?php if ($recipient === null): ?>
                        <?php continue; ?>
                    <?php endif; ?>

                    <div class="list-group-item p-3">

                        <div
                            class="d-flex flex-column flex-md-row
                                justify-content-between gap-3"
                        >
                            <div>
                                <div class="fw-semibold">
                                    <?= h(trim(
                                        ($recipient->firstname ?? '')
                                        . ' '
                                        . ($recipient->lastname ?? ''),
                                    )) ?>
                                </div>

                                <?php if ($recipient->phone !== null): ?>
                                    <small class="text-muted">
                                        <?= h($recipient->phone) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <?php if ($action->url !== null): ?>
                                <a
                                    href="<?= h($action->url) ?>"
                                    class="btn btn-success"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Ouvrir WhatsApp
                                </a>
                            <?php endif; ?>
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
