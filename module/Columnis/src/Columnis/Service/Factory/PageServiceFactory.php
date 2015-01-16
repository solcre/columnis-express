<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Service\PageService;
use Guzzle\Http\Client as GuzzleClient;

class PageServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Page
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $clientNumber = '001';
        $httpClient = new GuzzleClient('http://api.columnis.com/');        
        $templateService = $serviceLocator->get('TemplateService');
        
        return new PageService($templateService, $httpClient, $clientNumber);
    }
}
