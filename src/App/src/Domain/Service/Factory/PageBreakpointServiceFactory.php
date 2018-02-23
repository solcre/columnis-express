<?php

namespace App\Domain\Service\Factory;

use App\Domain\Service\PageBreakpointService;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageBreakpointServiceFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $templatesPathStack = [];
        $assetsManagerPaths = [];

        $config = $container->get('config');

        if (isset($config['template_path_stack'])) {
            $templatesPathStack = $config['template_path_stack'];
        }

        if (isset($config['asset_manager']['resolver_configs']['paths'])) {
            $assetsManagerPaths = $config['asset_manager']['resolver_configs']['paths'];
        }
        $templateRenderer = $container->get(TemplateRendererInterface::class);
        return new PageBreakpointService($templateRenderer, $templatesPathStack, $assetsManagerPaths);
    }
}
