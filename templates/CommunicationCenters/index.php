<?php
/**
 * @var \Cake\View\View $this
 * @var array<string, \CommunicationCenter\Recipient\Provider\RecipientProviderInterface> $providers
 * @var string|null $providerName
 * @var array<int, \CommunicationCenter\Recipient\Recipient> $recipients
 * @var \CommunicationCenter\Channel\ChannelInterface $whatsApp
 */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div class="mb-4">
                <h1 class="h3 mb-2">
                    Communication Center
                </h1>

                <p class="text-muted mb-0">
                    Préparez et gérez vos communications avec vos destinataires.
                </p>
            </div>

            <?php if (empty($providers)): ?>

                <div class="alert alert-info mb-0">
                    Aucun fournisseur de destinataires n'est actuellement enregistré.
                </div>

            <?php else: ?>

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <div class="mb-4">
                            <label for="provider" class="form-label fw-semibold">
                                Source des destinataires
                            </label>

                            <select
                                id="provider"
                                class="form-select"
                                onchange="if (this.value) window.location.href = '?provider=' + encodeURIComponent(this.value)"
                            >
                                <option value="">
                                    Choisir une source
                                </option>

                                <?php foreach ($providers as $name => $provider): ?>
                                    <option
                                        value="<?= h($name) ?>"
                                        <?= $providerName === $name ? 'selected' : '' ?>
                                    >
                                        <?= h($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if (!empty($recipients)): ?>

                            <?= $this->Form->create(null, [
                                'url' => [
                                    'plugin' => 'CommunicationCenter',
                                    'controller' => 'CommunicationCenters',
                                    'action' => 'prepare',
                                ],
                            ]) ?>

                            <?= $this->Form->hidden('provider', [
                                'value' => $providerName,
                            ]) ?>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <h2 class="h5 mb-0">
                                    Destinataires
                                </h2>

                                <span
                                    class="badge text-bg-secondary"
                                    id="selected-count"
                                >
                                    0 / 0 sélectionné
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-success"
                                    id="select-all"
                                >
                                    Tout sélectionner
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    id="unselect-all"
                                >
                                    Tout désélectionner
                                </button>
                            </div>

                            <div class="list-group">

                                <?php foreach ($recipients as $recipient): ?>
                                    <?php $canUseWhatsApp = $whatsApp->supports($recipient); ?>

                                    <label class="list-group-item d-flex gap-3 align-items-center">

                                        <input
                                            class="form-check-input flex-shrink-0 recipient-checkbox"
                                            type="checkbox"
                                            name="recipients[]"
                                            value="<?= h($recipient->externalId) ?>"
                                            <?= !$canUseWhatsApp ? 'disabled' : '' ?>
                                        >

                                        <span class="flex-grow-1">

                                            <span class="fw-semibold d-block">
                                                <?= h(trim(
                                                    ($recipient->firstname ?? '')
                                                    . ' '
                                                    . ($recipient->lastname ?? ''),
                                                )) ?>
                                            </span>

                                            <span class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                <?php if ($recipient->phone !== null): ?>
                                                    <small class="text-muted">
                                                        <?= h($recipient->phone) ?>
                                                    </small>
                                                <?php endif; ?>

                                                <?php if ($canUseWhatsApp): ?>
                                                    <span class="badge text-bg-success">
                                                        WhatsApp
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-warning">
                                                        Numéro invalide
                                                    </span>
                                                <?php endif; ?>
                                            </span>

                                        </span>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                            <div class="card mt-4 shadow-sm">
                                <div class="card-body">

                                    <h2 class="h5 mb-3">
                                        Message
                                    </h2>

                                    <?= $this->Form->control('message', [
                                        'type' => 'textarea',
                                        'label' => false,
                                        'rows' => 7,
                                        'class' => 'form-control',
                                        'placeholder' => 'Écrivez votre message...',
                                        'value' => "Bonjour {{firstname}},\n\n",
                                    ]) ?>

                                    <div class="form-text mt-2">
                                        Variables disponibles :
                                        <code>{{firstname}}</code>
                                        <code>{{lastname}}</code>
                                    </div>

                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button
                                    type="submit"
                                    class="btn btn-success btn-lg"
                                    id="prepare-button"
                                    disabled
                                >
                                    Préparer les messages WhatsApp
                                </button>
                            </div>

                            <?= $this->Form->end() ?>

                        <?php elseif (!empty($providerName)): ?>

                            <div class="alert alert-warning mb-0">
                                Aucun destinataire n'est disponible pour cette source.
                            </div>

                        <?php endif; ?>

                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll(
        '.recipient-checkbox:not(:disabled)'
    );

    const counter = document.getElementById('selected-count');
    const selectAll = document.getElementById('select-all');
    const unselectAll = document.getElementById('unselect-all');
    const prepareButton = document.getElementById('prepare-button');

    const updateCounter = () => {
        const selected = Array.from(checkboxes).filter(
            (checkbox) => checkbox.checked
        ).length;

        if (counter) {
            counter.textContent =
                `${selected} / ${checkboxes.length} sélectionné` +
                (selected > 1 ? 's' : '');
        }

        if (prepareButton) {
            prepareButton.disabled = selected === 0;

            prepareButton.textContent = selected > 0
                ? `Préparer ${selected} message${selected > 1 ? 's' : ''} WhatsApp`
                : 'Préparer les messages WhatsApp';
        }
    };

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateCounter);
    });

    selectAll?.addEventListener('click', () => {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = true;
        });

        updateCounter();
    });

    unselectAll?.addEventListener('click', () => {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });

        updateCounter();
    });

    updateCounter();
});
</script>
