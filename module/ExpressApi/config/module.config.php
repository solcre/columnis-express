<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'ColumnisExpress\\Adapters\\ApiAdapter' => 'ColumnisExpress\\Adapters\\ApiAdapterFactory',
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => 'ExpressApi\\V1\\Rpc\\Templates\\TemplatesControllerFactory',
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => 'ExpressApi\\V1\\Rpc\\InvalidatePage\\InvalidatePageControllerFactory',
            'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => 'ExpressApi\\V1\\Rpc\\FinishSetup\\FinishSetupControllerFactory',
            'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => 'ExpressApi\\V1\\Rpc\\SetMode\\SetModeControllerFactory',
            'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => 'ExpressApi\\V1\\Rpc\\GetMode\\GetModeControllerFactory',
            'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => 'ExpressApi\\V1\\Rpc\\ClearCache\\ClearCacheControllerFactory',
            'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => 'ExpressApi\\V1\\Rpc\\Statistics\\StatisticsControllerFactory',
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
            'express.rpc.statistics' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/statistics',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\Statistics\\Controller',
                        'action' => 'statistics',
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
            'express-api.rpc.finish-setup' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/setup/finish',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller',
                        'action' => 'finishSetup',
                    ),
                ),
            ),
            'express-api.rpc.set-mode' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/modes/:mode_id/set',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\SetMode\\Controller',
                        'action' => 'setMode',
                    ),
                ),
            ),
            'express-api.rpc.get-mode' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/modes',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\GetMode\\Controller',
                        'action' => 'getMode',
                    ),
                ),
            ),
            'express-api.rpc.clear-cache' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/express_api/cache/clear',
                    'defaults' => array(
                        'controller' => 'ExpressApi\\V1\\Rpc\\ClearCache\\Controller',
                        'action' => 'clearCache',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'express.rpc.templates',
            1 => 'express.rpc.invalidate-page',
            2 => 'express-api.rpc.finish-setup',
            3 => 'express-api.rpc.set-mode',
            4 => 'express-api.rpc.get-mode',
            5 => 'express-api.rpc.clear-cache',
            6 => 'express.rpc.statistics',
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
        'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => array(
            'service_name' => 'Statistics',
            'http_methods' => array(
                0 => 'GET',
            ),
            'route_name' => 'express.rpc.statistics',
        ),
        'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
            'service_name' => 'InvalidatePage',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'express.rpc.invalidate-page',
        ),
        'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => array(
            'service_name' => 'FinishSetup',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'express-api.rpc.finish-setup',
        ),
        'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => array(
            'service_name' => 'SetMode',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'express-api.rpc.set-mode',
        ),
        'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => array(
            'service_name' => 'GetMode',
            'http_methods' => array(
                0 => 'GET',
            ),
            'route_name' => 'express-api.rpc.get-mode',
        ),
        'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => array(
            'service_name' => 'ClearCache',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'express-api.rpc.clear-cache',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => 'Json',
            'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => 'Json',
        ),
        'accept_whitelist' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
        ),
        'content_type_whitelist' => array(
            'ExpressApi\\V1\\Rpc\\Templates\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\InvalidatePage\\Controller' => array(
                0 => 'application/vnd.express.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
                1 => 'application/json',
            ),
            'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => array(
                0 => 'application/vnd.express-api.v1+json',
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
            'ExpressApi\\V1\\Rpc\\Statistics\\Controller' => array(
                'actions' => array(
                    'templates' => array(
                        'GET' => true,
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
            'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => array(
                'actions' => array(
                    'finishSetup' => array(
                        'GET' => false,
                        'POST' => false,
                        'PATCH' => false,
                        'PUT' => false,
                        'DELETE' => false,
                    ),
                ),
            ),
            'ExpressApi\\V1\\Rpc\\SetMode\\Controller' => array(
                'actions' => array(
                    'setMode' => array(
                        'GET' => false,
                        'POST' => true,
                        'PATCH' => false,
                        'PUT' => false,
                        'DELETE' => false,
                    ),
                ),
            ),
            'ExpressApi\\V1\\Rpc\\GetMode\\Controller' => array(
                'actions' => array(
                    'getMode' => array(
                        'GET' => false,
                        'POST' => false,
                        'PATCH' => false,
                        'PUT' => false,
                        'DELETE' => false,
                    ),
                ),
            ),
            'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => array(
                'actions' => array(
                    'clearCache' => array(
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
    'zf-content-validation' => array(
        'ExpressApi\\V1\\Rpc\\FinishSetup\\Controller' => array(
            'input_filter' => 'ExpressApi\\V1\\Rpc\\FinishSetup\\Validator',
        ),
        'ExpressApi\\V1\\Rpc\\ClearCache\\Controller' => array(
            'input_filter' => 'ExpressApi\\V1\\Rpc\\ClearCache\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'ExpressApi\\V1\\Rpc\\FinishSetup\\Validator' => array(
            0 => array(
                'name' => 'dbname',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Database name',
            ),
            1 => array(
                'name' => 'dbuser',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Database user',
            ),
            2 => array(
                'name' => 'dbpassword',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Database password',
            ),
            3 => array(
                'name' => 'client_number',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Api client number',
            ),
            4 => array(
                'name' => 'api_base_url',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Api base URL',
            ),
            5 => array(
                'name' => 'api_version',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Api version',
            ),
            6 => array(
                'name' => 'user',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Cpanel user',
            ),
            7 => array(
                'name' => 'pass',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Cpanel password',
            ),
        ),
        'ExpressApi\\V1\\Rpc\\ClearCache\\Validator' => array(
            0 => array(
                'name' => 'dir',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
                'description' => 'Name of cache to clear, separate with (,).',
                'allow_empty' => false,
                'continue_if_empty' => false,
            ),
        ),
    ),
);
