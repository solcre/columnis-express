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
        $collections = array();

        if (isset($config['asset_manager']['resolver_configs']['collections'])) {
            $collections = $config['asset_manager']['resolver_configs']['collections'];
        }
        if (isset($config['view_manager']['template_path_stack'])) {
            $templatesPath = $config['view_manager']['template_path_stack'];
        }
        
        $templateAssetsResolver = new TemplateAssetsResolver($templatesPath, $template, $collections);

        return $templateAssetsResolver;
    }
}
