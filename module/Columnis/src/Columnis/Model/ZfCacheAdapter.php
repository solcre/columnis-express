<?php

namespace Columnis\Model;

use Zend\Cache\Storage\StorageInterface;
use GuzzleHttp\Subscriber\Cache\CacheStorageInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

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
        return $this->storageCache->hasItem($this->buildKey($request->getUrl()));
    }

    public function cache(RequestInterface $request, ResponseInterface $response) {
        return $this->storageCache->setItem($this->buildKey($request->getUrl()), $response);
    }

    public function delete(RequestInterface $request) {
        return $this->storageCache->removeItem($this->buildKey($request->getUrl()));
    }

    public function fetch(RequestInterface $request) {
        return $this->storageCache->getItem($this->buildKey($request->getUrl()));
    }

    public function purge($url) {
        return $this->storageCache->removeItem($this->buildKey($url));
    }

    private function buildKey($url) {
        return md5($url);
    }
}

?>