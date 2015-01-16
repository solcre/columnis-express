<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Models\Template;

class TemplateFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Template
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {   
        return new Template();
    }
}
