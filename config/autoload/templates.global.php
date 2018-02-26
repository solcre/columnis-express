<?php

use App\Domain\Utils\ArrayExtension;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigEnvironmentFactory;
use Zend\Expressive\Twig\TwigRendererFactory;

return [
    'dependencies' => [
        'factories' => [
            Twig_Environment::class          => TwigEnvironmentFactory::class,
            TemplateRendererInterface::class => TwigRendererFactory::class,
        ],
    ],

    'templates' => [
        'extension' => 'html.twig',
    ],

    'twig' => [
        'cache_dir'       => 'data/cache/twig',
        'assets_url'      => '/',
        'assets_version'  => null,
        'extensions'      => [
            'array' => new ArrayExtension()
        ],
        'runtime_loaders' => [
            // runtime loader names or instances
        ],
        'globals'         => [
            'dev' => false
        ],
        // 'timezone' => 'default timezone identifier; e.g. America/Chicago',
    ],
];
