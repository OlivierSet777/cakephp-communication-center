<?php
/**
 * @var \Cake\View\View $this
 * @var array<string, \CommunicationCenter\Recipient\Provider\RecipientProviderInterface> $providers
 * @var string|null $providerName
 * @var array<int, \CommunicationCenter\Recipient\Recipient> $recipients
 * @var \CommunicationCenter\Channel\ChannelInterface $channel
 * @var array<string, \CommunicationCenter\Channel\ChannelInterface> $channels
 * @var string $channelName
 * @var array<string, array<string, mixed>> $filters
 * @var array<string, string> $criteria
 * @var array<int, \CommunicationCenter\Model\Entity\CommunicationTemplate> $templates
 * @var array{
 *     campaigns: int,
 *     processing: int,
 *     completed: int,
 *     archived: int
 * } $stats
 */
?>

<?= $this->Html->css(
    'CommunicationCenter.communication-center',
    ['block' => true],
) ?>

<?php
$channelLabels = [
    'whatsapp' => 'WhatsApp',
    'email' => 'Email',
];

$channelLabel = $channelLabels[$channelName] ?? ucfirst($channelName);

$templateData = [];

foreach ($templates as $template) {
    $templateData[(string)$template->id] = [
        'subject' => $template->subject ?? '',
        'message' => $template->message_template,
    ];
}
?>

<div class="container py-4 communication-center">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <div
                class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center
                    gap-3 mb-4"
            >
                <div>
                    <h1 class="h3 mb-2 communication-title">
                        Communication Center
                    </h1>

                    <p class="text-muted mb-0 communication-muted">
                        Préparez et suivez vos campagnes de communication.
                    </p>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <?= $this->Html->link(
                        'Modèles',
                        [
                            'plugin' => 'CommunicationCenter',
                            'controller' => 'CommunicationTemplates',
                            'action' => 'index',
                        ],
                        [
                            'class' => 'btn btn-outline-secondary',
                        ],
                    ) ?>

                    <?= $this->Html->link(
                        'Campagnes',
                        [
                            'action' => 'campaigns',
                        ],
                        [
                            'class' => 'btn btn-outline-secondary',
                        ],
                    ) ?>

                    <?= $this->Html->link(
                        'Archives',
                        [
                            'action' => 'archives',
                        ],
                        [
                            'class' => 'btn btn-outline-secondary',
                        ],
                    ) ?>
                </div>
            </div>

            <div class="row g-3 mb-4">

                <div class="col-6 col-lg-3">
                    <div class="communication-stat h-100">
                        <div class="communication-stat__value">
                            <?= $stats['campaigns'] ?>
                        </div>
                        <div class="communication-stat__label">
                            Campagnes actives
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="communication-stat h-100">
                        <div class="communication-stat__value">
                            <?= $stats['processing'] ?>
                        </div>
                        <div class="communication-stat__label">
                            En cours
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="communication-stat h-100">
                        <div class="communication-stat__value">
                            <?= $stats['completed'] ?>
                        </div>
                        <div class="communication-stat__label">
                            Terminées
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="communication-stat h-100">
                        <div class="communication-stat__value">
                            <?= $stats['archived'] ?>
                        </div>
                        <div class="communication-stat__label">
                            Archivées
                        </div>
                    </div>
                </div>

            </div>

            <?php if (empty($providers)): ?>

                <div class="alert alert-info mb-0">
                    Aucun fournisseur de destinataires n'est actuellement enregistré.
                </div>

            <?php else: ?>

                <div class="card shadow-sm communication-create-card">
                    <div class="card-body p-3 p-md-4">

                        <div class="mb-4">
                            <div class="mb-3">
                                <h2 class="h5 mb-1 communication-subtitle">
                                    Créer une campagne
                                </h2>

                                <p class="small communication-muted mb-0">
                                    Choisissez un canal et une source pour afficher
                                    les destinataires compatibles.
                                </p>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="channel" class="form-label fw-semibold">
                                        Canal
                                    </label>

                                    <select
                                        id="channel"
                                        class="form-select"
                                        data-current-provider="<?= h((string)$providerName) ?>"
                                    >
                                        <?php foreach ($channels as $name => $availableChannel): ?>
                                            <?php $label = $channelLabels[$name] ?? ucfirst($name); ?>
                                            <option
                                                value="<?= h($name) ?>"
                                                <?= $channelName === $name ? 'selected' : '' ?>
                                            >
                                                <?= h($label) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="provider" class="form-label fw-semibold">
                                        Source des destinataires
                                    </label>

                                    <select
                                        id="provider"
                                        class="form-select"
                                        data-current-channel="<?= h($channelName) ?>"
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
                            </div>

                            <?php if (!empty($filters)): ?>
                                <div class="row g-3 mt-1">
                                    <?php foreach ($filters as $name => $filter): ?>
                                        <div class="col-12 col-md-6">
                                            <label
                                                for="criteria-<?= h($name) ?>"
                                                class="form-label fw-semibold"
                                            >
                                                <?= h($filter['label'] ?? ucfirst($name)) ?>
                                            </label>

                                            <select
                                                id="criteria-<?= h($name) ?>"
                                                class="form-select communication-criteria"
                                                data-criteria-name="<?= h($name) ?>"
                                            >
                                                <option value="">
                                                    Tous
                                                </option>

                                                <?php foreach (($filter['options'] ?? []) as $value => $label): ?>
                                                    <option
                                                        value="<?= h($value) ?>"
                                                        <?= ($criteria[$name] ?? null) === $value
                                                            ? 'selected'
                                                            : '' ?>
                                                    >
                                                        <?= h($label) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($recipients)): ?>

                            <?= $this->Form->create(null, [
                                'url' => [
                                    'plugin' => 'CommunicationCenter',
                                    'controller' => 'CommunicationCenters',
                                    'action' => 'prepare',
                                ],
                            ]) ?>

                            <?= $this->Form->hidden('channel', [
                                'value' => $channelName,
                            ]) ?>

                            <?= $this->Form->hidden('provider', [
                                'value' => $providerName,
                            ]) ?>

                            <?php foreach ($criteria as $name => $value): ?>
                                <?= $this->Form->hidden(
                                    sprintf('criteria[%s]', $name),
                                    [
                                        'value' => $value,
                                    ],
                                ) ?>
                            <?php endforeach; ?>

                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                                <h2 class="h5 mb-0 communication-subtitle">
                                    Destinataires
                                </h2>

                                <span
                                    class="communication-status communication-status--neutral align-self-start align-self-sm-auto"
                                    id="selected-count"
                                >
                                    0 / 0 sélectionné
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-communication-success"
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
                                    <?php
                                    $canUseChannel = $channel->supports($recipient);
                                    $contactValue = $channelName === 'email'
                                        ? $recipient->email
                                        : $recipient->phone;
                                    $invalidLabel = $channelName === 'email'
                                        ? 'Email invalide'
                                        : 'Numéro invalide';
                                    ?>

                                    <label class="list-group-item communication-recipient-item">
                                        <div class="d-flex align-items-start gap-3">

                                            <input
                                                class="form-check-input mt-1 communication-checkbox recipient-checkbox"
                                                type="checkbox"
                                                name="recipients[]"
                                                value="<?= h($recipient->externalId) ?>"
                                                <?= !$canUseChannel ? 'disabled' : '' ?>
                                            >

                                            <div class="flex-grow-1">
                                                <div class="fw-semibold communication-recipient-name">
                                                    <?= h(trim(
                                                        ($recipient->firstname ?? '')
                                                        . ' '
                                                        . ($recipient->lastname ?? ''),
                                                    )) ?>
                                                </div>

                                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                    <?php if ($contactValue !== null): ?>
                                                        <small class="text-muted communication-muted">
                                                            <?= h($contactValue) ?>
                                                        </small>
                                                    <?php endif; ?>

                                                    <?php if ($canUseChannel): ?>
                                                        <span class="communication-status communication-status--processed">
                                                            <?= h($channelLabel) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="communication-status communication-status--warning">
                                                            <?= h($invalidLabel) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </label>

                                <?php endforeach; ?>

                            </div>

                            <div class="card mt-4 shadow-sm">
                                <div class="card-body p-3 p-md-4">

                                    <div class="mb-4">
                                        <label
                                            for="message-template"
                                            class="form-label fw-semibold"
                                        >
                                            Modèle de message
                                        </label>

                                        <select
                                            id="message-template"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Aucun modèle
                                            </option>

                                            <?php foreach ($templates as $template): ?>
                                                <option value="<?= h((string)$template->id) ?>">
                                                    <?= h($template->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <div class="form-text communication-help">
                                            Seuls les modèles actifs du canal
                                            <?= h($channelLabel) ?> sont proposés.
                                        </div>
                                    </div>

                                    <?php if ($channelName === 'email'): ?>

                                        <h2 class="h5 mb-3 communication-subtitle">
                                            Objet
                                        </h2>

                                        <?= $this->Form->text('subject', [
                                            'class' => 'form-control',
                                            'placeholder' => 'Objet de votre email',
                                            'required' => true,
                                            'aria-label' => 'Objet de votre email',
                                            'id' => 'campaign-subject',
                                        ]) ?>

                                        <div class="form-text communication-help mb-3">
                                            L’objet est obligatoire pour les campagnes Email.
                                        </div>

                                    <?php endif; ?>

                                    <h2 class="h5 mb-3 communication-subtitle">
                                        Message
                                    </h2>

                                    <?= $this->Form->control('message', [
                                        'type' => 'textarea',
                                        'label' => false,
                                        'rows' => 7,
                                        'class' => 'form-control',
                                        'placeholder' => 'Écrivez votre message...',
                                        'value' => "Bonjour {{firstname}},\n\n",
                                        'id' => 'campaign-message',
                                    ]) ?>

                                    <div class="form-text mt-2 communication-help">
                                        Variables disponibles :
                                        <code>{{firstname}}</code>
                                        <code>{{lastname}}</code>
                                    </div>

                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button
                                    type="submit"
                                    class="btn btn-communication-success btn-lg"
                                    id="prepare-button"
                                    disabled
                                >
                                    Préparer les messages <?= h($channelLabel) ?>
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
    const channelSelect = document.getElementById('channel');
    const providerSelect = document.getElementById('provider');
    const templateSelect = document.getElementById('message-template');
    const subjectInput = document.getElementById('campaign-subject');
    const messageInput = document.getElementById('campaign-message');

    const templateData = <?= json_encode(
        $templateData,
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
    ) ?>;

    const criteriaSelects = document.querySelectorAll(
        '.communication-criteria'
    );

    const navigateWithFilters = (provider, channel) => {
        const params = new URLSearchParams();

        if (provider) {
            params.set('provider', provider);
        }

        if (channel) {
            params.set('channel', channel);
        }

        criteriaSelects.forEach((select) => {
            if (!select.value) {
                return;
            }

            params.set(
                `criteria[${select.dataset.criteriaName}]`,
                select.value,
            );
        });

        const query = params.toString();

        window.location.href = query
            ? `?${query}`
            : window.location.pathname;
    };

    channelSelect?.addEventListener('change', () => {
        navigateWithFilters(
            channelSelect.dataset.currentProvider ?? '',
            channelSelect.value,
        );
    });

    providerSelect?.addEventListener('change', () => {
        navigateWithFilters(
            providerSelect.value,
            providerSelect.dataset.currentChannel ?? '',
        );
    });

    criteriaSelects.forEach((select) => {
        select.addEventListener('change', () => {
            navigateWithFilters(
                providerSelect?.value ?? '',
                channelSelect?.value ?? '',
            );
        });
    });

    templateSelect?.addEventListener('change', () => {
        const data = templateData[templateSelect.value];

        if (!data) {
            return;
        }

        if (subjectInput) {
            subjectInput.value = data.subject ?? '';
        }

        if (messageInput) {
            messageInput.value = data.message ?? '';
        }
    });

    const checkboxes = document.querySelectorAll(
        '.recipient-checkbox:not(:disabled)'
    );

    const counter = document.getElementById('selected-count');
    const selectAll = document.getElementById('select-all');
    const unselectAll = document.getElementById('unselect-all');
    const prepareButton = document.getElementById('prepare-button');

    const channelLabel = <?= json_encode(
        $channelLabel,
        JSON_THROW_ON_ERROR,
    ) ?>;

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
                ? `Préparer ${selected} message${selected > 1 ? 's' : ''} ${channelLabel}`
                : `Préparer les messages ${channelLabel}`;
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
