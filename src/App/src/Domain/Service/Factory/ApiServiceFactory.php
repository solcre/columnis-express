<?php

namespace App\Domain\Service\Factory;

use App\Domain\Exception\Api\ApiBaseUrlNotSetException;
use App\Domain\Exception\Api\ClientNumberNotSetException;
use App\Domain\Service\ApiService;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Container\ContainerInterface;

class ApiServiceFactory
{


    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $columnisConfig = $config['columnis'] ?? array();
        $apiConfig = $columnisConfig['api_settings'] ?? array();

        if (!isset($apiConfig['client_number'])) {
            throw new ClientNumberNotSetException('There is no client_number set in local.php config file.');
        }
        if (!isset($apiConfig['api_base_url'])) {
            throw new ApiBaseUrlNotSetException('There is no api_base_url set in local.php config file.');
        }

        $clientNumber = $apiConfig['client_number'];
        $apiUrl = $apiConfig['api_base_url'];
        $httpClient = new GuzzleClient(['base_uri' => $apiUrl]);

//        $cacheConfig = isset($config['guzzle_cache']) ? $config['guzzle_cache'] : array();
//        if(isset($cacheConfig['adapter'])) {
//            $cache = StorageFactory::factory($cacheConfig);
//            $zfCacheAdapter = new ZfCacheAdapter($cache);
//            $cacheSubscriber = new CacheSubscriber($zfCacheAdapter, function(RequestInterface $request) use ($zfCacheAdapter){
//                return !$zfCacheAdapter->contains($request);
//            });
//            $httpClient->getEmitter()->attach($cacheSubscriber);
//        }

        return new ApiService($httpClient, $clientNumber);
    }
}
