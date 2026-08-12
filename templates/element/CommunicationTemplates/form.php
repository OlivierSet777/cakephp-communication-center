<?php
/**
 * @var \Cake\View\View $this
 * @var \CommunicationCenter\Model\Entity\CommunicationTemplate $template
 * @var string $submitLabel
 */
?>

<?= $this->Form->create($template) ?>

<div class="card shadow-sm">
    <div class="card-body p-3 p-md-4">

        <div class="row g-3">
            <div class="col-12 col-md-7">
                <?= $this->Form->control('name', [
                    'label' => 'Nom du modèle',
                    'class' => 'form-control',
                    'placeholder' => 'Ex. Rappel de cotisation',
                    'required' => true,
                ]) ?>
            </div>

            <div class="col-12 col-md-5">
                <?= $this->Form->control('channel', [
                    'label' => 'Canal',
                    'class' => 'form-select',
                    'options' => [
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                    ],
                    'empty' => 'Choisir un canal',
                    'required' => true,
                    'id' => 'template-channel',
                ]) ?>
            </div>
        </div>

        <div
            class="mt-3"
            id="template-subject-wrapper"
        >
            <?= $this->Form->control('subject', [
                'label' => 'Objet de l’email',
                'class' => 'form-control',
                'placeholder' => 'Objet du message',
            ]) ?>

            <div class="form-text communication-help">
                Utilisé uniquement pour les modèles Email.
            </div>
        </div>

        <div class="mt-3">
            <?= $this->Form->control('message_template', [
                'type' => 'textarea',
                'label' => 'Message',
                'rows' => 8,
                'class' => 'form-control',
                'placeholder' => 'Écrivez votre message...',
                'required' => true,
            ]) ?>

            <div class="form-text communication-help">
                Variables disponibles :
                <code>{{firstname}}</code>
                <code>{{lastname}}</code>
            </div>
        </div>

        <div class="mt-3">
            <?= $this->Form->control('active', [
                'type' => 'checkbox',
                'label' => 'Modèle actif',
                'class' => 'form-check-input',
                'default' => true,
            ]) ?>
        </div>

    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
    <?= $this->Html->link(
        'Annuler',
        [
            'plugin' => 'CommunicationCenter',
            'controller' => 'CommunicationTemplates',
            'action' => 'index',
        ],
        [
            'class' => 'btn btn-outline-secondary',
        ],
    ) ?>

    <?= $this->Form->button(
        $submitLabel,
        [
            'type' => 'submit',
            'class' => 'btn btn-communication-success',
        ],
    ) ?>
</div>

<?= $this->Form->end() ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const channel = document.getElementById('template-channel');
    const subjectWrapper = document.getElementById(
        'template-subject-wrapper'
    );

    const updateSubjectVisibility = () => {
        if (!subjectWrapper) {
            return;
        }

        subjectWrapper.hidden = channel?.value !== 'email';
    };

    channel?.addEventListener(
        'change',
        updateSubjectVisibility,
    );

    updateSubjectVisibility();
});
</script>
