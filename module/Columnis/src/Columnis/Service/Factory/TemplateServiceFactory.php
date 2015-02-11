<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Service\TemplateService;

class TemplateServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Template
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $templatesPathStack = array();
        
        $config      = $serviceLocator->get('Config');
        
        if (isset($config['view_manager']['template_path_stack'])) {
            $templatesPathStack = $config['view_manager']['template_path_stack'];
        }
        
        return new TemplateService($templatesPathStack);
    }
}
