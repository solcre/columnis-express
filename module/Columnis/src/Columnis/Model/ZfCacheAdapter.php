<?php

namespace Columnis\Model;

use Zend\Cache\Storage\StorageInterface;
use GuzzleHttp\Subscriber\Cache\CacheStorageInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Message\MessageFactory;

class ZfCacheAdapter implements CacheStorageInterface {

    /**
     *
     * @var StorageInterface 
     */
    private $storageCache = null;

    /**
     * @param StorageInterface $cache Zend Framework 2 cache adapter
     */
    public function __construct(StorageInterface $cache) {
        $this->storageCache = $cache;
    }

    public function contains(RequestInterface $request) {
        $ret = $this->storageCache->hasItem($this->buildKey($request));
        return $ret;
    }

    public function cache(RequestInterface $request, ResponseInterface $response) {
        return $this->storageCache->setItem($this->buildKey($request), $response);
    }

    public function delete(RequestInterface $request) {
        return $this->storageCache->removeItem($this->buildKey($request));
    }

    public function fetch(RequestInterface $request) {
        $message = $this->storageCache->getItem($this->buildKey($request));
	
	if (!$message) {
	    return null;
	}
	
	$factory = new MessageFactory();
	$response = $factory->fromMessage($message);
	
	if ($response->getStatusCode() != 200) {
	    return null;
	}
	        
	return $response;
    }

    public function purge($url) {
	// @@TODO REVISAR ESTE METODO
        return $this->storageCache->removeItem($this->buildKey($url));
    }

    private function buildKey(RequestInterface $request) {        
        return md5($request->getUrl());
    }
}

?>
