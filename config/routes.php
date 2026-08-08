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

        $builder->fallbacks();
    },
);
