<?php
declare(strict_types=1);

namespace CommunicationCenter\Controller;

use Cake\Controller\Controller;
use Cake\Http\Response;
use CommunicationCenter\Model\Table\CommunicationTemplatesTable;

/**
 * Communication Templates Controller.
 */
class CommunicationTemplatesController extends Controller
{
    /**
     * Initialize controller.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
    }

    /**
     * List communication templates.
     *
     * @param \CommunicationCenter\Model\Table\CommunicationTemplatesTable $templates
     *   Communication templates table.
     * @return void
     */
    public function index(
        CommunicationTemplatesTable $templates,
    ): void {
        $query = $templates
            ->find()
            ->orderBy([
                'CommunicationTemplates.created' => 'DESC',
            ]);

        $communicationTemplates = $this->paginate($query, [
            'limit' => 20,
            'maxLimit' => 50,
        ]);

        $this->set(compact(
            'communicationTemplates',
        ));
    }

    /**
     * Add a communication template.
     *
     * @param \CommunicationCenter\Model\Table\CommunicationTemplatesTable $templates
     *   Communication templates table.
     * @return \Cake\Http\Response|null
     */
    public function add(
        CommunicationTemplatesTable $templates,
    ): ?Response {
        $template = $templates->newEmptyEntity();

        if ($this->request->is('post')) {
            $template = $templates->patchEntity(
                $template,
                $this->request->getData(),
            );

            if ($templates->save($template)) {
                $this->Flash->success(
                    'Le modèle de message a été créé.',
                );

                return $this->redirect([
                    'action' => 'index',
                ]);
            }

            $this->Flash->error(
                'Impossible de créer le modèle de message.',
            );
        }

        $this->set(compact(
            'template',
        ));

        return null;
    }

    /**
     * Edit a communication template.
     *
     * @param int $id Template identifier.
     * @param \CommunicationCenter\Model\Table\CommunicationTemplatesTable $templates
     *   Communication templates table.
     * @return \Cake\Http\Response|null
     */
    public function edit(
        int $id,
        CommunicationTemplatesTable $templates,
    ): ?Response {
        $template = $templates->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $template = $templates->patchEntity(
                $template,
                $this->request->getData(),
            );

            if ($templates->save($template)) {
                $this->Flash->success(
                    'Le modèle de message a été modifié.',
                );

                return $this->redirect([
                    'action' => 'index',
                ]);
            }

            $this->Flash->error(
                'Impossible de modifier le modèle de message.',
            );
        }

        $this->set(compact(
            'template',
        ));

        return null;
    }

    /**
     * Toggle a communication template active status.
     *
     * @param int $id Template identifier.
     * @param \CommunicationCenter\Model\Table\CommunicationTemplatesTable $templates
     *   Communication templates table.
     * @return \Cake\Http\Response
     */
    public function toggle(
        int $id,
        CommunicationTemplatesTable $templates,
    ): Response {
        $this->request->allowMethod(['post']);

        $template = $templates->get($id);

        $template->active = !$template->active;

        if ($templates->save($template)) {
            $this->Flash->success(
                $template->active
                    ? 'Le modèle a été activé.'
                    : 'Le modèle a été désactivé.',
            );
        } else {
            $this->Flash->error(
                'Impossible de modifier le statut du modèle.',
            );
        }

        return $this->redirect([
            'action' => 'index',
        ]);
    }
}
