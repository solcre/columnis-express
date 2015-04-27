<?php
use Zend\Uri\UriFactory;
require __DIR__ . '/src/ExpressApi/Module.php';
UriFactory::registerScheme('chrome-extension', 'Zend\Uri\Uri');