<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Model\HtmlCache;
 
class HtmlCacheFactory implements FactoryInterface
{
 
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Configure the cache
        $config = $serviceLocator->get('Config');
        $cacheConfig = isset($config['cache']) ? $config['cache'] : array();
        $cache = StorageFactory::factory($cacheConfig);
        
        return new HtmlCache($cache);
    }
}
