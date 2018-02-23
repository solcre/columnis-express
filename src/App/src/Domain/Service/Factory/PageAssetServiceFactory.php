<?php

namespace App\Domain\Service\Factory;

use App\Domain\Service\PageAssetService;
use App\Domain\Service\TemplateService;
use Psr\Container\ContainerInterface;

class PageAssetServiceFactory
{


    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        return new PageAssetService($config);
    }
}
