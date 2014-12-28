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
                        'seo' => '[a-zA-Z-_]+',
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
            'TemplateAssetsResolver' => 'Columnis\Service\Factory\TemplateAssetsResolverFactory',
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
            'collections' => array(
                'css/fixed.css' => array(
                    'css/style.css',
                    'css/prueba.css',
                ),
            ),
            'paths' => array(
                realpath(__DIR__ . '/../../../public'),
            ),
        ),
        'resolvers' => array(
            'TemplateAssetsResolver' => 2000,
        ),
    ),
);
