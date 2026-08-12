<?php
/**
 * @var \Cake\View\View $this
 * @var iterable<\CommunicationCenter\Model\Entity\CommunicationTemplate> $communicationTemplates
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
                        Modèles de messages
                    </h1>

                    <p class="text-muted mb-0 communication-muted">
                        Créez et gérez vos messages réutilisables.
                    </p>
                </div>

                <?= $this->Html->link(
                    'Ajouter un modèle',
                    [
                        'plugin' => 'CommunicationCenter',
                        'controller' => 'CommunicationTemplates',
                        'action' => 'add',
                    ],
                    [
                        'class' => 'btn btn-communication-success',
                    ],
                ) ?>
            </div>

            <div class="d-flex flex-wrap gap-2 mb-4">
                <?= $this->Html->link(
                    'Nouvelle campagne',
                    [
                        'plugin' => 'CommunicationCenter',
                        'controller' => 'CommunicationCenters',
                        'action' => 'index',
                    ],
                    [
                        'class' => 'btn btn-outline-secondary',
                    ],
                ) ?>

                <?= $this->Html->link(
                    'Campagnes',
                    [
                        'plugin' => 'CommunicationCenter',
                        'controller' => 'CommunicationCenters',
                        'action' => 'campaigns',
                    ],
                    [
                        'class' => 'btn btn-outline-secondary',
                    ],
                ) ?>
            </div>

            <?php if ($communicationTemplates->items()->isEmpty()): ?>

                <div class="alert alert-info">
                    Aucun modèle de message n'a encore été créé.
                </div>

            <?php else: ?>

                <div class="d-flex flex-column gap-3">

                    <?php foreach ($communicationTemplates as $template): ?>
                        <?php
                        $channelLabel = match ($template->channel) {
                            'whatsapp' => 'WhatsApp',
                            'email' => 'Email',
                            default => ucfirst($template->channel),
                        };
                        ?>

                        <div class="card shadow-sm">
                            <div class="card-body p-3 p-md-4">

                                <div class="row g-3 align-items-start">

                                    <div class="col-12 col-md">

                                        <div
                                            class="d-flex flex-wrap
                                                align-items-center gap-2 mb-3"
                                        >
                                            <h2 class="h5 mb-0">
                                                <?= h($template->name) ?>
                                            </h2>

                                            <span
                                                class="communication-status
                                                    communication-status--neutral"
                                            >
                                                <?= h($channelLabel) ?>
                                            </span>

                                            <span
                                                class="communication-status
                                                    <?= $template->active
                                                        ? 'communication-status--processed'
                                                        : 'communication-status--warning' ?>"
                                            >
                                                <?= $template->active
                                                    ? 'Actif'
                                                    : 'Inactif' ?>
                                            </span>
                                        </div>

                                        <?php if (
                                            $template->channel === 'email'
                                            && !empty($template->subject)
                                        ): ?>
                                            <div class="mb-3">
                                                <div
                                                    class="small
                                                        communication-muted"
                                                >
                                                    Objet
                                                </div>

                                                <div class="fw-semibold">
                                                    <?= h($template->subject) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div
                                            class="p-3 rounded
                                                communication-message-preview"
                                        >
                                            <?= nl2br(
                                                h($template->message_template),
                                            ) ?>
                                        </div>

                                    </div>

                                    <div class="col-12 col-md-auto">
                                        <div
                                            class="d-flex flex-column
                                                flex-sm-row gap-2"
                                        >
                                            <?= $this->Html->link(
                                                'Modifier',
                                                [
                                                    'plugin' => 'CommunicationCenter',
                                                    'controller' => 'CommunicationTemplates',
                                                    'action' => 'edit',
                                                    $template->id,
                                                ],
                                                [
                                                    'class' => 'btn btn-outline-secondary',
                                                ],
                                            ) ?>

                                            <?= $this->Form->postLink(
                                                $template->active
                                                    ? 'Désactiver'
                                                    : 'Activer',
                                                [
                                                    'plugin' => 'CommunicationCenter',
                                                    'controller' => 'CommunicationTemplates',
                                                    'action' => 'toggle',
                                                    $template->id,
                                                ],
                                                [
                                                    'class' => $template->active
                                                        ? 'btn btn-outline-danger'
                                                        : 'btn btn-outline-communication-success',
                                                    'confirm' => $template->active
                                                        ? 'Désactiver ce modèle ?'
                                                        : 'Activer ce modèle ?',
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

            <?php if ($communicationTemplates->pageCount() > 1): ?>
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

        </div>
    </div>
</div>
