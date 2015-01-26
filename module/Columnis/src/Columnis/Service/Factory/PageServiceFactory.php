<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Service\PageService;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Cache\Zf2CacheAdapter;
use Zend\Cache\StorageFactory;

class PageServiceFactory implements FactoryInterface {

    /**
     * {@inheritDoc}
     *
     * @return Page
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $clientNumber = '001';
        $httpClient = new GuzzleClient('http://api.columnis.com/');
        
        $config = $serviceLocator->get('Config');
        $cacheConfig = isset($config['guzzle_cache']) ? $config['guzzle_cache'] : array();
        $cache = StorageFactory::factory($cacheConfig);
        $adapter = new Zf2CacheAdapter($cache);
        $cachePlugin = new CachePlugin($adapter);
        
        $httpClient->addSubscriber($cachePlugin);


        $templateService = $serviceLocator->get('TemplateService');
        /* @var \Columnis\Service\TemplateService $templateService */

        return new PageService($templateService, $httpClient, $clientNumber);
    }

}
