<?php

namespace App\Domain\Service\Factory;

use App\Domain\Exception\ConfigNotFoundException;
use App\Domain\Service\PageAssetService;
use Psr\Container\ContainerInterface;

class PageAssetServiceFactory
{


    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        if (empty($config['template_assets_resolver'])) {
            throw new ConfigNotFoundException('template_assets_resolver config not found', 404);
        }
        if (empty($config['asset_manager'])) {
            throw new ConfigNotFoundException('asset_manager config not found', 404);
        }
        return new PageAssetService($config);
    }
}
