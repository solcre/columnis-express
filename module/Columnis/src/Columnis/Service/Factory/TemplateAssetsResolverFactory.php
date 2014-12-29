<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Model\TemplateAssetsResolver;

class TemplateAssetsResolverFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return TemplateAssetsResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $template = 'home';
        
        $config      = $serviceLocator->get('Config');
        
        if (isset($config['view_manager']['template_path_stack'])) {
            $templatesPathStack = $config['view_manager']['template_path_stack'];
        }
        if (isset($config['asset_manager']['resolver_configs']['paths'])) {
            $assetsPaths = $config['asset_manager']['resolver_configs']['paths'];
        }
        $templateAssetsResolver = new TemplateAssetsResolver($assetsPaths, $templatesPathStack, $template);

        return $templateAssetsResolver;
    }
}
