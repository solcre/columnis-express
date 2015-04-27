<?php

namespace Columnis;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Uri\UriFactory;

class Module {

    public function onBootstrap(MvcEvent $e) {
        UriFactory::registerScheme('chrome-extension', 'Zend\Uri\Uri');

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        // get the cache listener service
        $cacheListener = $e->getApplication()->getServiceManager()->get('Columnis\Model\CacheListener');

        // attach the listeners to the event manager
        $e->getApplication()->getEventManager()->attach($cacheListener);
    }

    public function getConfig() {
        return include __DIR__.'/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
                ),
            ),
        );
    }
}
