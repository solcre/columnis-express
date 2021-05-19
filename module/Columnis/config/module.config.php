<?php

return array(
    'router' => array(
        'routes' => array(
            'columnis' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '[/:lang]/:seo{-}-:pageId',
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
            'columnisDefault' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Columnis\Controller',
                        'controller' => 'Page',
                        'action' => 'index',
                        'cache' => true
                    ),
                ),
            ),
            'columnisDefaultLang' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/:lang[/]',
                    'constraints' => array(
                        'lang' => 'espanol|english|portugues',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Columnis\Controller',
                        'controller' => 'Page',
                        'action' => 'index',
                        'cache' => true
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Cache' => 'Zend\Cache\Service\StorageCacheFactory',
            'Columnis\Model\HtmlCache' => 'Columnis\Service\Factory\HtmlCacheFactory',
            'Columnis\Model\CacheListener' => 'Columnis\Service\Factory\CacheListenerFactory',
            'Columnis\Model\TemplateAssetsResolver' => 'Columnis\Service\Factory\TemplateAssetsResolverFactory',
            'Columnis\Service\ApiService' => 'Columnis\Service\Factory\ApiServiceFactory',
            'Columnis\Service\PageService' => 'Columnis\Service\Factory\PageServiceFactory',
            'Columnis\Service\PageBreakpointService' => 'Columnis\Service\Factory\PageBreakpointServiceFactory',
            'Columnis\Service\TemplateService' => 'Columnis\Service\Factory\TemplateServiceFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
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
            'error/404' => __DIR__ . '/../view/error/404.tpl',
            'error/index' => __DIR__ . '/../view/error/index.tpl',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../../../public_html/templates',
        ),
    ),
    'cache' => array(
        'adapter' => 'htmlcache',
        'options' => array(
            'cache_dir' => 'public_html/pages'
        )
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__ . '/../../../public_html/css',
                __DIR__ . '/../../../public_html/js',
            ),
        ),
        'resolvers' => array(
            'Columnis\Model\TemplateAssetsResolver' => 2000,
        )
    ),
    'template_assets_resolver' => array(
        'match_patterns' => array(
            'template_name' => '/^templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            'global_asset' => '/^(css|js)\/fixed\/.+\.(css|js)$/'
        ),
        'global_folder_name' => 'fixed',
        'search_exclude' => [
            '/templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            '/(css|js)\/fixed\/minified\.(css|js)$/',
        ]
    ),
);
