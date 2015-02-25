<?php
return array(
    'controllers' => array(
        'factories' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => 'ExpressApi\\V1\\Rpc\\Templates\\TemplatesControllerFactory',
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => 'ExpressApi\\V1\\Rpc\\InvalidatePage\\InvalidatePageControllerFactory',
        ),
    ),
    'router' => array(
        'routes' => array(
            'express.rpc.templates' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/templates',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\Templates\\Controller',
                        'action' => 'templates',
                    ),
                ),
            ),
            'express.rpc.invalidate-page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/pages/:page_id/invalidate',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller',
                        'action' => 'invalidatePage',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'express.rpc.templates',
            1 => 'express.rpc.invalidate-page',
        ),
    ),
    'zf-rpc' => array(
        'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
            'service_name' => 'Templates',
            'http_methods' => array(
                0 => 'GET',
            ),
            'route_name' => 'express.rpc.templates',
        ),
        'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
            'service_name' => 'InvalidatePage',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'express.rpc.invalidate-page',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => 'Json',
        ),
        'accept_whitelist' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
        ),
        'content_type_whitelist' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-mvc-auth' => array(
        'authorization' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
                'actions' => array(
                    'templates' => array(
                        'GET' => false,
                        'POST' => false,
                        'PATCH' => false,
                        'PUT' => false,
                        'DELETE' => false,
                    ),
                ),
            ),
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
                'actions' => array(
                    'invalidatePage' => array(
                        'GET' => false,
                        'POST' => true,
                        'PATCH' => false,
                        'PUT' => false,
                        'DELETE' => false,
                    ),
                ),
            ),
        ),
    ),
);
