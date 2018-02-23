<?php

namespace App;

use App\Action\Factory\PagesFactory;
use App\Domain\Service\ApiService;
use App\Domain\Service\Factory\ApiServiceFactory;
use App\Domain\Service\Factory\PageAssetServiceFactory;
use App\Domain\Service\Factory\PageBreakpointServiceFactory;
use App\Domain\Service\Factory\PageServiceFactory;
use App\Domain\Service\Factory\TemplateServiceFactory;
use App\Domain\Service\PageAssetService;
use App\Domain\Service\PageBreakpointService;
use App\Domain\Service\PageService;
use App\Domain\Service\TemplateService;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [
            ],
            'factories'  => [
                Action\TemplatesAction::class => Action\Factory\TemplatesFactory::class,
                Action\PagesAction::class     => PagesFactory::class,
                TemplateService::class        => TemplateServiceFactory::class,
                PageService::class            => PageServiceFactory::class,
                ApiService::class             => ApiServiceFactory::class,
                PageAssetService::class       => PageAssetServiceFactory::class,
                PageBreakpointService::class  => PageBreakpointServiceFactory::class
            ],
        ];
    }

    /**
     * Returns the templates configuration
     *
     * @return array
     */
    public function getTemplates()
    {
        return [
            'paths' => [
                'app'       => [__DIR__ . '/../templates/app'],
                'error'     => [__DIR__ . '/../templates/error'],
                'layout'    => [__DIR__ . '/../templates/layout'],
                'templates' => [__DIR__ . '/../../../public/templates'],
            ],
        ];
    }
}
