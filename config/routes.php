<?php
declare(strict_types=1);

use Cake\Routing\RouteBuilder;

/**
 * Communication Center routes.
 *
 * @var \Cake\Routing\RouteBuilder $routes
 */
$routes->plugin(
    'CommunicationCenter',
    ['path' => '/communication-center'],
    function (RouteBuilder $builder): void {
        $builder->connect(
            '/',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'index',
            ],
        );

        $builder->connect(
            '/campaigns',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'campaigns',
            ],
        );

        $builder->connect(
            '/campaigns/{id}',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'campaign',
            ],
        )
            ->setPatterns([
                'id' => '\d+',
            ])
            ->setPass(['id']);

        $builder->connect(
            '/campaigns/{id}/archive',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'archiveCampaign',
            ],
        )
            ->setMethods(['POST'])
            ->setPatterns([
                'id' => '\d+',
            ])
            ->setPass(['id']);

        $builder->connect(
            '/campaigns/archives',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'archives',
            ],
        );

        $builder->connect(
            '/campaigns/{id}/restore',
            [
                'controller' => 'CommunicationCenters',
                'action' => 'restoreCampaign',
            ],
        )
            ->setMethods(['POST'])
            ->setPatterns([
                'id' => '\d+',
            ])
            ->setPass(['id']);

        $builder->connect(
            '/templates',
            [
                'controller' => 'CommunicationTemplates',
                'action' => 'index',
            ],
        );

        $builder->connect(
            '/templates/add',
            [
                'controller' => 'CommunicationTemplates',
                'action' => 'add',
            ],
        );

        $builder->connect(
            '/templates/{id}/edit',
            [
                'controller' => 'CommunicationTemplates',
                'action' => 'edit',
            ],
        )
            ->setPatterns([
                'id' => '\d+',
            ])
            ->setPass(['id']);

        $builder->connect(
            '/templates/{id}/toggle',
            [
                'controller' => 'CommunicationTemplates',
                'action' => 'toggle',
            ],
        )
            ->setMethods(['POST'])
            ->setPatterns([
                'id' => '\d+',
            ])
            ->setPass(['id']);

        $builder->fallbacks();
    },
);
