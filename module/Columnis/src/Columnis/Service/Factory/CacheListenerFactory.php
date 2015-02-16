<?php

namespace Columnis\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Columnis\Model\CacheListener;
use Zend\Cache\StorageFactory;

class CacheListenerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return CacheListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // @@TODO Esto es correcto que vaya aca?
        $adapterPluginManager = StorageFactory::getAdapterPluginManager();
        $adapterPluginManager->setInvokableClass('htmlcache', 'Columnis\Model\HtmlCache');
        
        $storageAdapter = $serviceLocator->get('Zend\Cache');
        $options = $storageAdapter->getOptions();
        $options->setNamespace('columnis');
        $cacheListener = new CacheListener($storageAdapter);
        
        return $cacheListener;
    }
}
