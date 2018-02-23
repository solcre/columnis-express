<?php

namespace App\Action\Factory;

use App\Action\PagesAction;
use App\Domain\Service\PageService;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class PagesFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $pageService = $container->get(PageService::class);
        $templateRenderer = $container->get(TemplateRendererInterface::class);
        return new PagesAction($pageService, $templateRenderer);
    }
}
