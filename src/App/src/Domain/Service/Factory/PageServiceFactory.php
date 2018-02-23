<?php

namespace App\Domain\Service\Factory;

use App\Domain\Service\ApiService;
use App\Domain\Service\PageBreakpointService;
use App\Domain\Service\PageService;
use App\Domain\Service\TemplateService;
use Psr\Container\ContainerInterface;

class PageServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $apiService = $container->get(ApiService::class);
        $templateService = $container->get(TemplateService::class);
        $pageBreakpointService = $container->get(PageBreakpointService::class);
        return new PageService($apiService, $templateService, $pageBreakpointService);
    }
}
