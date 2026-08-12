<?php
/**
 * @var \Cake\View\View $this
 * @var \CommunicationCenter\Model\Entity\CommunicationTemplate $template
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
                    Ajouter un modèle
                </h1>

                <p class="text-muted mb-0 communication-muted">
                    Créez un message réutilisable pour vos futures campagnes.
                </p>
            </div>

            <?= $this->element(
                'CommunicationCenter.CommunicationTemplates/form',
                [
                    'template' => $template,
                    'submitLabel' => 'Créer le modèle',
                ],
            ) ?>

        </div>
    </div>
</div>
