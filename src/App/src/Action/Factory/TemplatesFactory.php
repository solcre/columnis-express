<?php

namespace App\Action\Factory;

use App\Action\TemplatesAction;
use App\Domain\Service\TemplateService;
use Psr\Container\ContainerInterface;

class TemplatesFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $templateService = $container->get(TemplateService::class);
        return new TemplatesAction($templateService);
    }
}
