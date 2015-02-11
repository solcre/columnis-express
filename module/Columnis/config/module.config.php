<?php

return array(
    'router' => array(
        'routes' => array(
            'columnis' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:lang/:seo{-}-:pageId',
                    'constraints' => array(
                        'lang' => 'espanol|english|portugues',
                        'seo' => '[a-zA-Z0-9-_]+',
                        'pageId' => '\d+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Columnis\Controller',
                        'controller' => 'Page',
                        'action' => 'index',
                        'cache' => true
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Cache' => 'Zend\Cache\Service\StorageCacheFactory',
            'HtmlCache' => 'Columnis\Service\Factory\HtmlCacheFactory',
            'CacheListener' => 'Columnis\Service\Factory\CacheListenerFactory',
            'ApiService' => 'Columnis\Service\Factory\ApiServiceFactory',
            'PageService' => 'Columnis\Service\Factory\PageServiceFactory',
            'TemplateService' => 'Columnis\Service\Factory\TemplateServiceFactory',
            'TemplateAssetsResolver' => 'Columnis\Service\Factory\TemplateAssetsResolverFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'shared' => array(
            'ApiService' => false,
            'PageService' => false,
            'TemplateService' => false,
            'TemplateAssetsResolver' => false
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Columnis\Controller\Page' => 'Columnis\Controller\PageController',
            'htmlcache' => 'Columnis\Model\HtmlCache'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../../../public/templates',
        ),
    ),
    'cache' => array(
        'adapter' => 'htmlcache',
        'options' => array(
            'cache_dir' => 'public/pages'
        )
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../../../public/css',
                __DIR__ . '/../../../public/js',
            ),
        ),
        'resolvers' => array(
            'TemplateAssetsResolver' => 2000,
        )
    ),
    'template_assets_resolver' => array(
        'match_patterns' => array(
            'template_name' => '/^templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            'global_asset' => '/^(css|js)\/fixed\/.+\.(css|js)$/'
        ),
        'global_folder_name' => 'fixed'
    ),
);
