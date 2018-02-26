<?php

namespace App\Domain\Service\Factory;

use App\Domain\Service\PageAssetService;
use App\Domain\Service\TemplateAssetsResolver;
use App\Domain\Service\TemplateService;
use Psr\Container\ContainerInterface;

class TemplateAssetsResolverFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $templateService = $container->get(TemplateService::class);
        $pageAssetService = $container->get(PageAssetService::class);
        return new TemplateAssetsResolver($templateService, $pageAssetService);
    }
}
