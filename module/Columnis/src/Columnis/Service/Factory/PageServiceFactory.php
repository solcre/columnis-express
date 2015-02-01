<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Service\PageService;

class PageServiceFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     *
     * @return Page
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {        
        $templateService = $serviceLocator->get('TemplateService');
        /* @var \Columnis\Service\TemplateService $templateService */
        
        $apiService = $serviceLocator->get('ApiService');
        /* @var \Columnis\Service\ApiService $apiService */

        return new PageService($templateService, $apiService);
    }

}
